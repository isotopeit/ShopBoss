<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Isotope\ShopBoss\Models\Product;
use Isotope\ShopBoss\Models\Category;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class XlImportController extends Controller
{

    public function xlProductCreate(Request $request)
    {
        try 
        {
            $data = $request->all();
            $category = Category::where('category_code',$request->category_code)
                                ->where('category_name',$request->category_name)
                                ->first();

            DB::beginTransaction();
            if(is_null($category))
                $category = $this->create_categoty($data);
                
            $check_product = Product::where('product_name',$data['product_name'])
                                    ->orWhere('product_code',$data['product_code'])
                                    ->first();

            if(!is_null($check_product))
                throw new Exception("Product Name or Code Already in Database", 403);

            Product::create([
                'category_id'               => $category->id,
                'product_name'              => $data['product_name'],
                'product_code'              => $data['product_code'],
                'product_barcode_symbology' => 'C39',
                'product_quantity'          => $data['product_quantity'],
                'product_cost'              => $data['product_cost'],
                'product_price'             => $data['product_price'],
                'product_unit'              => $data['product_unit'],
                'product_stock_alert'       => $data['product_stock_alert'],
                'product_order_tax'         => 0,
            ]);

            DB::commit();
                
            return response()->json([
                'msg' => 'Product Create Successfully'
            ],200);


        } catch (Exception $e) 
        {
            DB::rollBack();
            return response()->json([
                'msg' => $e->getMessage(),
            ],400);
        }
    }

    private function create_categoty($data)
    {
        return Category::create([
            'category_code' => $data['category_code'],
            'category_name' => $data['category_name'],
        ]);
    }
}