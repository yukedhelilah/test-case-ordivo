<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Helpers\JsonFormatter;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Transaction;
use App\Models\TransactionItem;
use DataTables;
use Exception;
use Validator;

class CartController extends Controller
{
    public function add(Request $request)
    {
        DB::beginTransaction();
        try {
            $validateData = Validator::make($request->all(), [
                'product_id' => 'required',
                'qty'        => 'required|gte:1',
            ]);

            if($validateData->fails()) {
                return JsonFormatter::error(
                    null,
                    $validateData->errors(),
                    405
                );
            }

            $product = Product::findOrFail($request->product_id);

            $input                 = $request->all();
            $input['price']        = $product->price;
            $input['subtotal']     = $product->price * $request->qty;
            $input['created_date'] = date('Y-m-d H:i:s');

            $data = Cart::create($input);
            DB::commit();

            return JsonFormatter::success(
                $data,
                'success create data'
            );
        } catch (Exception $error) {
            DB::rollback();
            return JsonFormatter::error(
                null,
                $error->getMessage(),
                500
            );
        }
    }

    public function checkout(Request $request)
    {
        DB::beginTransaction();
        try {
            $validateData = Validator::make($request->all(), [
                'cart_id' => 'required',
                'note'    => 'nullable'
            ]);

            if($validateData->fails()) {
                return JsonFormatter::error(
                    null,
                    $validateData->errors(),
                    405
                );
            }

            $order['created_date'] = date("Y-m-d H:i:s");
            $transaction = Transaction::create($order);

            $cart = $request->cart_id;
            $note = $request->note;

            foreach ($cart as $key => $value) {
                $item = Cart::findOrFail($value);
                $order_item = [
                    'transaction_id' => $transaction->transaction_id,
                    'product_id'     => $item->product_id,
                    'qty'            => $item->qty,
                    'price'          => $item->price,
                    'subtotal'       => $item->subtotal,
                    'note'           => $note[$key],
                ];
                TransactionItem::create($order_item);
                $item->delete();
            }
            DB::commit();

            return JsonFormatter::success(
                $transaction,
                'success create data'
            );
        } catch (Exception $error) {
            DB::rollback();
            return JsonFormatter::error(
                null,
                $error->getMessage(),
                500
            );
        }
    }

    public function list(Request $request)
    {
        try {
            $data = Cart::orderBy('created_date', 'desc')->get();
            $data = DataTables::of($data)->make();

            return JsonFormatter::datatables(
                $data,
                'success get data'
            );
        } catch (Exception $error) {
            return JsonFormatter::error(
                null,
                $error->getMessage(),
                500
            );
        }
    }
}
