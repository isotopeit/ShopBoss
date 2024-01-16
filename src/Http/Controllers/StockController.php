<?php

namespace Isotope\ShopBoss\Http\Controllers;

use App\Http\Controllers\Controller;
use Isotope\ShopBoss\Models\PurchaseDetail;

class StockController extends Controller
{
    public function stock()
    {
        $req = request()->all();
        $data = PurchaseDetail::query()
                    ->when(array_key_exists('product_code',$req) && strlen($req['product_code']) > 0, function($q) use($req){
                        $q->where('product_code', 'LIKE', '%'.$req['product_code'].'%');
                    })
                    ->when(array_key_exists('product_name',$req) && strlen($req['product_name']) > 0, function($q) use($req){
                        $q->where('product_name', 'LIKE', '%'.$req['product_name'].'%');
                    })
                    ->where('available_qty','>',0.0001)
                    ->selectRaw('
                        product_name,
                        product_code,
                        unit_price,
                        sum(available_qty) as stock_qty
                    ')
                    ->groupBy('product_id')
                    ->paginate(20);
        return view('shopboss::stock.stock',compact('data'));
    }
}
