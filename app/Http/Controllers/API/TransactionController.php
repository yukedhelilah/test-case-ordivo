<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Helpers\JsonFormatter;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Exception;
use Validator;
use DB;

class TransactionController extends Controller
{
    public function list(Request $request)
    {
        try {
            $validateData = Validator::make($request->all(), [
                'transaction_id' => 'required',
            ]);

            if($validateData->fails()) {
                return JsonFormatter::error(
                    null,
                    $validateData->errors(),
                    405
                );
            }

            $data = TransactionItem::where('transaction_id',$request->transaction_id)->get();

            return JsonFormatter::success(
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

    public function order(Request $request)
    {
        DB::beginTransaction();
        try {
            $validateData = Validator::make($request->all(), [
                'transaction_id' => 'required',
                'name'           => 'required',
                'address'        => 'required',
                'phone'          => 'required',
            ]);

            if($validateData->fails()) {
                return JsonFormatter::error(
                    null,
                    $validateData->errors(),
                    405
                );
            }

            $transaction = Transaction::findOrFail($request->transaction_id);
            $total       = TransactionItem::sum('subtotal');

            $order = [
                'name'    => $request->name,
                'address' => $request->address,
                'phone'   => $request->phone,
                'total'   => $total,
                'flag'    => 1,
            ];
            $data = $transaction->update($order);
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
}
