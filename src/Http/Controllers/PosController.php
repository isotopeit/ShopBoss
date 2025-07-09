<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Isotope\ShopBoss\Models\Sale;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Isotope\ShopBoss\Models\Product;
use Isotope\ShopBoss\Models\Customer;
use Isotope\ShopBoss\Models\SaleDetails;
use Isotope\ShopBoss\Models\SalePayment;
use Isotope\ShopBoss\Models\PurchaseDetail;

class PosController extends Controller
{
    public static $permissions = [
        'index'   => ['access_pos', 'POS List'],
        'store'   => ['store_pos', 'POS Store'],
    ];
    
    public function index() {
        $customers = Customer::selectRaw("
                            id,
                            concat(customer_name, ' [', customer_phone, ']') as text
                        ")
                        ->where('branch_id', Auth::user()->branch->id)
                        ->get();
        return view('shopboss::pos.index', compact('customers'));
    }


    public function store(Request $request) {
        $request->validate([
            'customer'              => 'required',
            'products'              => 'required',
            'cash_on_hand'          => 'required',
            'products.*.product_id' => 'required|integer',
            'products.*.qty'        => 'required|integer',
        ]);

        try {
            $req = $request->all();

            if(count($req['products']) < 1)
                throw new Exception(__('Select Product'), 403);

            DB::beginTransaction();

            $products = [];

            foreach ($req['products'] as $item) {
                $product  = Product::find($item['product_id']);

                $purchase_detail = PurchaseDetail::query()
                                    ->where('product_id',$product->id)
                                    ->where('available_qty','>',0.0001)
                                    ->where('available_qty','>=',$item['qty'])
                                    ->orderBy('created_at')
                                    ->first();

                if(is_null($purchase_detail))
                    throw new Exception("Stock Problem, Please Call Development Team", 403);
                    

                array_push($products, [
                    // 'branch_id'               => 1,
                    'product_id'              => $product->id,
                    'purchase_detail_id'      => $purchase_detail->id,
                    'product_name'            => $product->product_name,
                    'product_code'            => $product->product_code,
                    'quantity'                => $item['qty'],
                    'price'                   => $product->product_price * floatval($item['qty']),
                    'unit_price'              => $product->product_price,
                    'sub_total'               => ($product->product_price) * floatval($item['qty']),
                    'product_tax_amount'      => 0,
                    'product_discount_amount' => 0
                ]);

                $purchase_detail->update([
                    'sale_qty'      => $purchase_detail->sale_qty + $item['qty'],
                    'available_qty' => $purchase_detail->available_qty - $item['qty'],
                ]);
            }


            $customer = Customer::find($req['customer']);
            if(is_null($customer)) throw new Exception(__('Customre not found'), 404);


            $totalSubTotal = collect($products)->sum('sub_total');
            $payload = [
                'customer_id'         => $customer->id,
                'customer_name'       => $customer->customer_name,
                'branch_id' => settings()->enable_branch == 1 ? Auth::user()->branch->id:null,
                'date'                => now(),
                'tax_percentage'      => $req['tax_percentage'],
                'tax_amount'          => ($totalSubTotal / 100) * $req['tax_percentage'],
                'discount_percentage' => (100/$totalSubTotal)*$req['discount_amount'],
                'discount_amount'     => $req['discount_amount'],
                'note'                => '',
                'shipping_amount'     => 0,
                'paid_amount'         => $req['cash_on_hand'],
                'payment_method'      => 'Cash',
                'payment_status'      => 'Paid',
            ];
            
            $payload['total_amount'] = $totalSubTotal + $payload['tax_amount'] - $req['discount_amount'];
            $payload['due_amount']   = $payload['total_amount'] - $payload['paid_amount'];

            $sale = Sale::create($payload);

            if ($payload['paid_amount'] > 0) {
                SalePayment::create([
                    'date'           => now(),
                    'reference'      => 'INV/' . $sale->reference,
                    'amount'         => $sale->paid_amount,
                    'sale_id'        => $sale->id,
                    'payment_method' => 'Cash'
                ]);
            }
            foreach ($products as $product) {
                SaleDetails::create(array_merge([
                    'sale_id' => $sale->id,
                    'branch_id' => settings()->enable_branch == 1 ? Auth::user()->branch->id:null,
                ], $product));
            }

            DB::commit();
            return redirect()->to('/app/pos')->withSuccess("POS Sale Created!");
        } catch (Exception $e) {
            return redirect()->to('/app/pos')->withErrors($e->getMessage());
        }
    }
}
