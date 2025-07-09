<?php

namespace Isotope\ShopBoss\Http\Controllers;

use App\Http\Controllers\Controller;
use Isotope\ShopBoss\Models\PurchaseDetail;

class StockController extends Controller
{
    public static $permissions = [
        'stock'   => ['stock_access', 'Stock'],
    ];

    public function stock()
    {
       $req = request()->all();

$data = PurchaseDetail::query()
    ->when(!empty($req['product_code']), function ($q) use ($req) {
        $q->where('product_code', 'LIKE', '%' . $req['product_code'] . '%');
    })
    ->when(!empty($req['product_name']), function ($q) use ($req) {
        $q->where('product_name', 'LIKE', '%' . $req['product_name'] . '%');
    })
    ->when(!empty($req['branch_id']), function ($q) use ($req) {
        $q->where('branch_id', $req['branch_id']);
    })
    ->where('available_qty', '>', 0.0001)
    ->selectRaw('
        product_id,
        product_name,
        product_code,
        branch_id,
        unit_price,
        SUM(available_qty) as stock_qty
    ')
    ->groupBy('product_id', 'branch_id', 'product_name', 'product_code', 'unit_price')
    ->paginate(20);

        return view('shopboss::stock.stock',compact('data'));
    }
}
