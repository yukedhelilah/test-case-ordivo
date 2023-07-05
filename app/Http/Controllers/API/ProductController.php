<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Helpers\JsonFormatter;
use Illuminate\Http\Request;
use App\Models\Product;
use Exception;
use Validator;
use DB;

class ProductController extends Controller
{
    public function create(Request $request)
    {
        DB::beginTransaction();
        try {
            $validateData = Validator::make($request->all(), [
                'product_name' => 'required|max:255',
                'product_desc' => 'required',
                'price'        => 'required|gte:1',
            ]);

            if($validateData->fails()) {
                return JsonFormatter::error(
                    null,
                    $validateData->errors(),
                    405
                );
            }

            $input = $request->all();
            $input['created_date'] = date('Y-m-d H:i:s');

            $data = Product::create($input);
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

    public function detail(Request $request)
    {
        try {
            $validateData = Validator::make($request->all(), [
                'product_id' => 'required',
            ]);

            if($validateData->fails()) {
                return JsonFormatter::error(
                    null,
                    $validateData->errors(),
                    405
                );
            }

            $data = Product::findOrFail($request->product_id);

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

    public function list(Request $request)
    {
        try {
            $data = Product::where('flag', 1)->orderBy('created_date', 'desc')->get();

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
}
