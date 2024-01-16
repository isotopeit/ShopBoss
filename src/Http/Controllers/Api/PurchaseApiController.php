<?php

namespace Isotope\ShopBoss\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Isotope\ShopBoss\Models\Product;
use Isotope\ShopBoss\Models\Purchase;
use Isotope\ShopBoss\Models\Sale;

class PurchaseApiController extends Controller
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

    public function purchaseSelect2(Request $request)
    {
        $req = $request->all();
        $purchase =  Purchase::selectRaw("
                            id,
                            reference as text
                        ")
                        ->where('supplier_id', $req['supplier'])
                        ->when(isset($req['reference']), function($q) use($req) {
                            $reference = $req['reference'];
                            $q->whereRaw("reference LIKE '%$reference%'");
                        })
                        ->orderByDesc('id')
                        ->limit(10)
                        ->get();
        return response()->json($purchase, 200);
    }

    public function saleSelect2(Request $request)
    {
        $req = $request->all();
        $purchase =  Sale::selectRaw("
                            id,
                            reference as text
                        ")
                        ->where('customer_id', $req['customer'])
                        ->when(isset($req['reference']), function($q) use($req) {
                            $reference = $req['reference'];
                            $q->whereRaw("reference LIKE '%$reference%'");
                        })
                        ->orderByDesc('id')
                        ->limit(10)
                        ->get();
        return response()->json($purchase, 200);
    }

    public function purchase($id)
    {
        try 
        {
            $purchase = Purchase::with('purchaseDetails')->find($id);
            if(is_null($purchase)) throw new Exception("Product not found", 404);
            
            return response()->json($purchase, 200);
        } 
        catch (Exception $e) {
            return response()->json([
                'msg'  => $e->getMessage(),
                'line' => $e->getLine()
            ], 400);
        }
    }

    public function sale($id)
    {
        try 
        {
            $sale = Sale::with('saleDetails')->find($id);
            if(is_null($sale)) throw new Exception("Product not found", 404);
            
            return response()->json($sale, 200);
        } 
        catch (Exception $e) {
            return response()->json([
                'msg'  => $e->getMessage(),
                'line' => $e->getLine()
            ], 400);
        }
    }

}
