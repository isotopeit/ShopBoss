<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Isotope\ShopBoss\Models\Sale;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Isotope\ShopBoss\Models\Product;
use Isotope\ShopBoss\Models\Customer;
use Isotope\ShopBoss\Models\SaleDetails;
use Isotope\ShopBoss\Models\SalePayment;

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
                        ")->get();
        return view('shopboss::pos.index', compact('customers'));
    }


    public function store(Request $request) {
        $request->validate([
            'customer'              => 'required',
            'products'              => 'required',
            'cash_on_hand'          => 'required',
            'refundable_amount'     => 'required',
            'products.*.product_id' => 'required|integer',
            'products.*.qty'        => 'required|integer',
        ]);

        try {
            $req = $request->all();
            DB::beginTransaction();

            $products = [];

            foreach ($req['products'] as $item) {
                $product  = Product::find($item['product_id']);
                array_push($products, [
                    'product_id'              => $product->id,
                    'product_name'            => $product->product_name,
                    'product_code'            => $product->product_code,
                    'quantity'                => $item['qty'],
                    'price'                   => $product->product_price * floatval($item['qty']),
                    'unit_price'              => $product->product_price,
                    'sub_total'               => $product->product_price * floatval($item['qty']),
                    'product_discount_amount' => 0,
                    'product_discount_type'   => 'fixed',
                    'product_tax_amount'      => 0,
                ]);
                $product->increment('product_quantity', $item['qty']);
            }

            $customer = Customer::findOrFail($req['customer']);

            $totalSubTotal = collect($products)->sum('sub_total');
            $payload = [
                'customer_id'         => $customer->id,
                'customer_name'       => $customer->customer_name,
                'date'                => now(),
                'tax_percentage'      => $req['tax_percentage'],
                'tax_amount'          => ($totalSubTotal / 100) * $req['tax_percentage'],
                'discount_percentage' => (100/$totalSubTotal)*$req['discount_amount'],
                'discount_amount'     => $req['discount_amount'],
                'note'                => '',
                'status'              => 'Completed',
                'shipping_amount'     => 0,
                'paid_amount'         => $req['cash_on_hand'] - $req['refundable_amount'],
                'payment_method'      => 'Cash',
                'payment_status'      => 'Paid',
            ];
            
            $payload['total_amount'] = $totalSubTotal + $payload['tax_amount'] - $req['discount_amount'];
            $payload['due_amount']   = $payload['total_amount'] - $payload['paid_amount'];
            if($req['refundable_amount'] < 0) {
                throw new Exception("Due amount must be 0", 400);
            }
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
                    'sale_id' => $sale->id
                ], $product));
            }

            DB::commit();
            return redirect()->to('/app/pos')->withSuccess("POS Sale Created!");
        } catch (Exception $e) {
            return redirect()->to('/app/pos')->withErrors($e->getMessage());
        }
    }
}
