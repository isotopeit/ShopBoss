<?php

namespace Isotope\ShopBoss\Http\Controllers;

use PDF;
use Exception;
use Illuminate\Http\Request;
use Isotope\ShopBoss\Models\Sale;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Isotope\ShopBoss\Models\Product;
use Isotope\ShopBoss\Models\Customer;
use Isotope\ShopBoss\Models\SaleDetails;
use Isotope\ShopBoss\Models\SalePayment;
use Isotope\ShopBoss\Models\PurchaseDetail;

class SaleController extends Controller
{
    public static $permissions = [
        'index'   => ['access_sales', 'Sale List'],
        'create'  => ['create_sales', 'Sale Create'],
        'store'   => ['store_sales', 'Sale Store'],
        'show'    => ['show_sales', 'Sale Show'],
        'edit'    => ['edit_sales', 'Sale Edit'],
        'pdf'     => ['pdf', 'Sale Pdf'],
        'posPdf'  => ['posPdf', 'Sale shopboss Pdf'],
        'update'  => ['update_sales', 'Sale Update'],
        'destroy' => ['delete_sales', 'Sale Delete'],
    ];

    public function pdf($id)
    {
        $sale = Sale::findOrFail($id);
        $customer = Customer::findOrFail($sale->customer_id);
        // return view('shopboss::sale.print', [
        //     'sale'     => $sale,
        //     'customer' => $customer,
        // ]);
        $pdf = PDF::loadview('shopboss::sale.print', [
            'sale'     => $sale,
            'customer' => $customer,
        ])->setPaper('a4');

        return $pdf->stream('sale-'. $sale->reference .'.pdf');
    }

    public function posPdf($id)
    {
        $sale = Sale::findOrFail($id);
        return view('shopboss::sale.print-pos-plan', compact('sale'));

        // $mpdf = new Mpdf([
        //     // 'mode'              => 'utf-8',
        //     'format'            => 'A5',
        //     'default_font'      => 'nikosh'
        // ]);
        // $view = view('shopboss::sale.print-shopboss', compact('sale'))->render();
        // $mpdf->WriteHTML($view);
        // $mpdf->Output('sale-'. $sale->reference . ".pdf", "I");

    }

    public function index()
    {
        $sales = Sale::search()->orderBydesc('id')->paginate(15);
        return view('shopboss::sale.index',compact('sales'));
    }

    public function create() {
        $customers = Customer::selectRaw("
                                        id,
                                        customer_name as text,
                                        customer_phone as subText
                                    ")
                                    ->get();
        return view('shopboss::sale.create',compact('customers'));
    }

    public function store(Request $request)
    {
        try {
            $req = $request->all();

            if(count($req['products']) < 1)
                throw new Exception(__('Select Product'), 403);

            DB::beginTransaction();

            $products = [];

            foreach ($req['products'] as $item) {
                $product  = Product::find($item['product_id']);
                $discount = array_key_exists('percentage', $item) ? ($product->product_price / 100) * floatval($item['discount']) : $item['discount'];

                $purchase_detail = PurchaseDetail::query()
                                    ->where('product_id',$product->id)
                                    ->where('available_qty','>',0.0001)
                                    ->where('available_qty','>=',$item['qty'])
                                    ->orderBy('created_at')
                                    ->first();

                if(is_null($purchase_detail))
                    throw new Exception("Stock Problem, Please Call Development Team", 403);


                array_push($products, [
                    'branch_id'               => 1,
                    'product_id'              => $product->id,
                    'purchase_detail_id'      => $purchase_detail->id,
                    'product_name'            => $product->product_name,
                    'product_code'            => $product->product_code,
                    'quantity'                => $item['qty'],
                    'price'                   => $product->product_price * floatval($item['qty']),
                    'unit_price'              => $product->product_price - $discount,
                    'sub_total'               => ($product->product_price - $discount) * floatval($item['qty']),
                    'product_discount_amount' => $discount,
                    'product_tax_amount'      => 0,
                ]);

                $purchase_detail->update([
                    'sale_qty'      => $purchase_detail->sale_qty + $item['qty'],
                    'available_qty' => $purchase_detail->available_qty - $item['qty'],
                ]);
            }

            $customer = Customer::find($req['customer_id']);
            if(is_null($customer)) throw new Exception(__('Customre not found'), 404);

            $totalSubTotal = collect($products)->sum('sub_total');
            $payload = [
                'branch_id'           => 1,
                'date'                => $req['date'],
                'customer_id'         => $customer->id,
                'customer_name'       => $customer->customer_name,
                'tax_percentage'      => $req['tax_percentage'],
                'tax_amount'          => ($totalSubTotal / 100) * $req['tax_percentage'],
                'discount_percentage' => (100/$totalSubTotal)*$req['discount_amount'],
                'discount_amount'     => $req['discount_amount'],
                'note'                => $req['note'],
                'shipping_amount'     => $req['shipping_amount'],
                'paid_amount'         => $req['paid_amount'],
                'payment_method'      => $req['payment_method'],
            ];

            $payload['total_amount'] = $totalSubTotal + $req['shipping_amount'] + $payload['tax_amount'] - $req['discount_amount'];
            $payload['due_amount']   = $payload['total_amount'] - $payload['paid_amount'];

            if ($payload['due_amount'] == $payload['total_amount']) {
                $payment_status = 'Unpaid';
            } elseif ($payload['due_amount'] > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }
            $payload['payment_status'] = $payment_status;
            $sale = Sale::create($payload);

            if ($payload['paid_amount'] > 0) {
                SalePayment::create([
                    'date'           => $req['date'],
                    'reference'      => 'INV/' . $sale->reference,
                    'amount'         => $sale->paid_amount,
                    'sale_id'        => $sale->id,
                    'payment_method' => $req['payment_method']
                ]);
            }
            foreach ($products as $product) {
                SaleDetails::create(array_merge([
                    'sale_id' => $sale->id
                ], $product));
            }

            DB::commit();
            return redirect()->route('sales.index')->withSuccess(__('Sale Created'));
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors($e->getMessage() .' || '. $e->getLine());
        }
    }

    public function show($id) {
        $sale = Sale::find($id);
        $customer = Customer::findOrFail($sale->customer_id);

        return view('shopboss::sale.show', compact('sale', 'customer'));
    }

    public function edit($id) {

        $customers = Customer::selectRaw("
                                    id,
                                    customer_name as text,
                                    customer_phone as subText
                                ")
                                ->get();

        $sale = Sale::with('saleDetails.product')->find($id);

        return view('shopboss::sale.edit', compact('sale','customers'));
    }

    public function update(Request $request, $id)
    {
        try
        {
            $req = $request->all();
            $sale = Sale::with('saleDetails.product')->find($id);

            DB::beginTransaction();

            foreach ($req['products'] as $item) {
                $product  = Product::find($item['product_id']);
                $discount = array_key_exists('percentage', $item) ? ($product->product_price / 100) * floatval($item['discount']) : $item['discount'];
                $payload  = [
                    'product_id'              => $product->id,
                    'product_name'            => $product->product_name,
                    'product_code'            => $product->product_code,
                    'quantity'                => $item['qty'],
                    'price'                   => $product->product_price * floatval($item['qty']),
                    'unit_price'              => $product->product_price,
                    'sub_total'               => ($product->product_price - $discount) * floatval($item['qty']),
                    'product_discount_amount' => $discount,
                    'product_tax_amount'      => 0,
                ];

                if (array_key_exists('detail_id', $item))
                {
                    $detail = SaleDetails::find($item['detail_id']);

                    $purchase_detail = PurchaseDetail::find($detail->purchase_detail_id);

                    $purchase_detail->update([
                        'sale_qty'      => ($purchase_detail->sale_qty - $detail->quantity) + $item['qty'],
                        'available_qty' => ($purchase_detail->available_qty + $detail->quantity) - $item['qty'],
                    ]);

                    $detail->update($payload);
                } else {

                    $purchase_detail = PurchaseDetail::query()
                            ->where('product_id',$product->id)
                            ->where('available_qty','>',0.0001)
                            ->where('available_qty','>=',$item['qty'])
                            ->orderBy('created_at')
                            ->first();

                    if(is_null($purchase_detail))
                         throw new Exception("Stock Problem, Please Call Development Team", 403);

                    $purchase_detail->update([
                        'sale_qty'      => $purchase_detail->sale_qty + $item['qty'],
                        'available_qty' => $purchase_detail->available_qty - $item['qty'],
                    ]);
                    $sale->saleDetails()->create($payload);

                }

            }

            $customer = Customer::findOrFail($request->customer_id);
            if(is_null($customer)) throw new Exception(__('Customer not found'), 404);

            $sale->refresh();
            $totalSubTotal = $sale->saleDetails->sum('sub_total');
            $payload = [
                'customer_id'         => $customer->id,
                'customer_name'       => $customer->customer_name,
                'date'                => $req['date'],
                'tax_percentage'      => $req['tax_percentage'],
                'tax_amount'          => ($totalSubTotal / 100) * $req['tax_percentage'],
                'discount_percentage' => (100/$totalSubTotal)*$req['discount_amount'],
                'discount_amount'     => $req['discount_amount'],
                'note'                => $req['note'],
                'shipping_amount'     => $req['shipping_amount'],
                'total_amount'        => $totalSubTotal + $req['shipping_amount'] + $sale->tax_amount - $req['discount_amount'],
            ];

            $payload['due_amount'] = $payload['total_amount'] - $sale->paid_amount;
            if ($payload['due_amount'] == $payload['total_amount']) {
                $payment_status = 'Unpaid';
            } elseif ($payload['due_amount'] > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }
            $payload['payment_status'] = $payment_status;

            $sale->update($payload);

            DB::commit();
            return redirect()->route('sales.index')->withSuccess(__('Sale Updated'));
        }
        catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors($e->getMessage() .'||' . $e->getLine());
        }
    }

    public function destroy($id) {
        $sale = Sale::find($id);
        $sale->salePayments()->delete();
        $sale->saleDetails()->delete();
        $sale->delete();

        return redirect()->route('sales.index')->withSuccess(__('Sale Deleted'));
    }
}
