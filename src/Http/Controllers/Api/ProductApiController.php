<?php

namespace Isotope\ShopBoss\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Isotope\ShopBoss\Models\Product;
use Isotope\ShopBoss\Models\PurchaseDetail;

class ProductApiController extends Controller
{
    // public static $permissions = [
    //     'index'   => ['access_purchases', 'Purchase List'],
    //     'create'  => ['create_purchases', 'Purchase Create'],
    //     'store'   => ['store_purchases', 'Purchase Store'],
    //     'show'    => ['show_purchases', 'Purchase Show'],
    //     'edit'    => ['edit_purchases', 'Purchase Edit'],
    //     'update'  => ['update_purchases', 'Purchase Update'],
    //     'destroy' => ['delete_purchases', 'Purchase Delete'],
    // ];

    public function productSelect2(Request $request)
    {
        $req = $request->all();
        $products = Product::selectRaw("
                product_code as id,
                product_name as text,
                product_code as subText
            ")
            ->when(isset($req['product']), function($q) use($req) {
                $product = $req['product'];
                $q->whereRaw("product_name LIKE '%$product%' OR product_code LIKE '%$product%'");
            })
            ->where('branch_id', Auth::user()->branch->id)
            ->limit(10)
            ->get();

        return response()->json($products, 200);
    }

    public function product($code)
    {
        try 
        {
            $product = Product::firstWhere('product_code', $code);
            if(is_null($product)) throw new Exception("Product not found", 404);
            $product->stock = PurchaseDetail::where('product_id',$product->id)->where('available_qty','>',0)->sum('available_qty');

            return response()->json($product, 200);
        } 
        catch (Exception $e) {
            return response()->json([
                'msg'  => $e->getMessage(),
                'line' => $e->getLine()
            ], 400);
        }
    }

}
