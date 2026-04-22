<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Isotope\Finance\Models\Bank;
use Isotope\Finance\Models\BankTransaction;
use Isotope\Finance\Models\FinanceParticular;
use Isotope\Finance\Models\FinanceRecord;
use Isotope\ShopBoss\Models\Branch;
use Isotope\ShopBoss\Models\Customer;
use Isotope\ShopBoss\Models\Product;
use Isotope\ShopBoss\Models\PurchaseDetail;
use Isotope\ShopBoss\Models\Sale;
use Isotope\ShopBoss\Models\SaleDetails;
use Isotope\ShopBoss\Models\SalePayment;
use Isotope\ShopBoss\Observers\SaleObserver;
use PDF;


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
        try {
        
            $sale = Sale::findOrFail($id);
            
            // Check branch access if enabled
            if (settings()->enable_branch == 1) {
                if (Auth::user()->branch && $sale->branch_id != Auth::user()->branch->id) {
                    return redirect()->route('sales.index')
                        ->withErrors('You do not have access to view this sale PDF.');
                }
            }
            
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
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('sales.index')
                ->withErrors('Sale not found or invalid ID.');
        }
    }

    public function posPdf($id)
    {
        $sale = Sale::findOrFail($id);
        
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $sale->branch_id != Auth::user()->branch->id) {
                return redirect()->route('sales.index')
                    ->withErrors('You do not have access to view this sale POS PDF.');
            }
        }
        
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
        $query = Sale::search();
        
        // Filter by branch if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch) {
                $query->where('branch_id', Auth::user()->branch->id);
            }
        }
        
        $sales = $query->orderByDesc('id')->paginate(15);
        
        
        return view('shopboss::sale.index', compact('sales',));
    }

    public function create() {
        // Get customers
        $query = Customer::selectRaw("id, customer_name as text, customer_phone as subText");
        
        // Filter customers by branch if enabled
        if (settings()->enable_branch == 1 && Auth::user()->branch) {
            $query->where('branch_id', Auth::user()->branch->id);
        }
        
        $customers = $query->get();
        
        // Get branches for dropdown
        $branches = [];
        if (settings()->enable_branch == 1) {
            $branches = Branch::all();
        }
        $paymentMethods = FinanceParticular::where('transactionable', 1)->get();
        $banks = Bank::all()->map(function ($bank) {
            return [
                'id' => $bank->id,
                'text' => $bank->name
            ];
        });
        
        return view('shopboss::sale.create', compact('customers', 'branches','paymentMethods', 'banks'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        try {
            $req = $request->all();
            if(count($req['products']) < 1)
                throw new Exception(__('Select Product'), 403);

             // Set branch_id with a proper default
            $branch_id = 1; // Default branch ID
            
            // If branch system is enabled, get branch_id from request or user
            if (settings()->enable_branch == 1) {
                if (isset($req['branch_id']) && !empty($req['branch_id'])) {
                    $branch_id = $req['branch_id'];
                } elseif (Auth::user()->branch) {
                    $branch_id = Auth::user()->branch->id;
                }
            }

            DB::beginTransaction();

            $products = [];

            foreach ($req['products'] as $item) {
                $product  = Product::find($item['product_id']);
                $discount = array_key_exists('percentage', $item) ? ($product->product_price / 100) * floatval($item['discount']) : $item['discount'];
                
                // Use branch_id from request if branch system is enabled, otherwise use default
                $branch_id = settings()->enable_branch == 1 ? $req['branch_id'] : null;
                $purchase_detail_query = PurchaseDetail::query()
                                    ->where('product_id', $product->id)
                                    ->where('available_qty', '>', 0.0001)
                                    ->where('available_qty', '>=', $item['qty'])
                                    ->orderBy('created_at');
                
                // Filter purchase details by branch if branch system is enabled
                if (settings()->enable_branch == 1) {
                    $purchase_detail_query->where('branch_id', $branch_id);
                }
                
                $purchase_detail = $purchase_detail_query->first();

                if(is_null($purchase_detail))
                    throw new Exception("Stock Problem, Please Call Development Team", 403);

                array_push($products, [
                    'branch_id'               => $branch_id,
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
            
            // Use branch_id from request if branch system is enabled, otherwise use default
            $branch_id = settings()->enable_branch == 1 ? $req['branch_id'] : null;
            
            $payload = [
                'branch_id'           => $branch_id,
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
                'payment_method'      => $req['payment_method_id'],
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
                    'payment_method' => $req['payment_method_id'],
                    'branch_id'      => $branch_id,
                ]);
            }
            foreach ($products as $product) {
                SaleDetails::create(array_merge([
                    'sale_id' => $sale->id
                ], $product));
            }

            $saleObserver = new SaleObserver();
            $saleObserver->created($sale);
             // Only handle bank transaction if payment method is bank
            $paymentMethod = FinanceParticular::find($req['payment_method_id'] ?? null);
            
            if(str_contains($paymentMethod->alias , 'bank') && !isset($req['bank_id']))
                throw new Exception("Bank is required for bank payment method", 400);

            if ($paymentMethod && str_contains($paymentMethod->alias , 'bank')) {
                $this->handleBankTransaction($req, $sale,$paymentMethod);
            }

            DB::commit();
            return redirect()->route('sales.index')->withSuccess(__('Sale Created'));
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors($e->getMessage() .' || '. $e->getLine());
        }
    }

      private function handleBankTransaction($req, $sale,$paymentMethod)
    {
        if (!array_key_exists('bank_id', $req) || !class_exists(Bank::class) || !class_exists(BankTransaction::class)) {
            return;
        }

        $bank = Bank::find($req['bank_id']);
        if (!$bank) {
            throw new Exception("Bank not found", 404);
        }

        $bank_last_record = BankTransaction::where('bank_id', $req['bank_id'])
                                ->orderByDesc('id')
                                ->first();

        $amount = !empty($req['paid_amount']) && $req['paid_amount'] > 0
                    ? $req['paid_amount']
                    : ($req['due_amount'] ?? 0);

        $previous_balance = $bank_last_record ? $bank_last_record->balance : 0;
        $status = 0;
        $balance = $previous_balance;

        if (!empty($req['paid_amount']) && $req['paid_amount'] > 0) {
            $balance += $amount;
            $status = 0; // credit
        } else {
            $balance -= $amount;
            if ($balance < 0) {
                throw new Exception("The Amount Cannot Be Greater Than The Balance", 403);
            }
            $status = 1; // debit
        }

        $description = "Payment of Create Sale : {$sale->reference} | Bank({$bank->name}:***" . substr($bank->account_number, -4) . ")";

        // Finance record তৈরি
        $financeRecordId = null;
        if (class_exists(FinanceRecord::class) && class_exists(FinanceParticular::class)) {
            $bankParticular = $paymentMethod;

            if ($bankParticular) {
                $operation = (!empty($req['paid_amount']) && $req['paid_amount'] > 0) ? 'increment' : 'decrement';
                $financeRecord = FinanceRecord::entry([
                    'description'     => $description,
                    'amount'          => $amount,
                    'reference_no'    => $sale->reference,
                    'recordable_type' => Sale::class,
                    'recordable_id'   => $sale->id,
                ], $bankParticular, $operation);

                if ($financeRecord) {
                    $financeRecordId = $financeRecord->id;
                }
            }
        }

        // BankTransaction তৈরি
        return BankTransaction::create([
            'bank_id'           => $bank->id,
            'finance_record_id' => $financeRecordId,
            'amount'            => $amount,
            'balance'           => $balance,
            'description'       => $description,
            'status'            => $status,
            'invoice_id'        => $sale->id,
        ]);
    }

    public function show($id) {
        $sale = Sale::find($id);
        
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $sale->branch_id != Auth::user()->branch->id) {
                return redirect()->route('sales.index')
                    ->withErrors('You do not have access to view this sale.');
            }
        }
        
        $customer = Customer::findOrFail($sale->customer_id);

        return view('shopboss::sale.show', compact('sale', 'customer'));
    }

    public function edit($id) {
        $sale = Sale::with('saleDetails.product')->find($id);
        
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $sale->branch_id != Auth::user()->branch->id) {
                return redirect()->route('sales.index')
                    ->withErrors('You do not have access to edit this sale.');
            }
        }

        // Get customers
        $query = Customer::selectRaw("id, customer_name as text, customer_phone as subText");
        
        // Filter customers by branch if enabled
        if (settings()->enable_branch == 1 && Auth::user()->branch) {
            $query->where('branch_id', Auth::user()->branch->id);
        }
        
        $customers = $query->get();
        
        // Get branches for dropdown
        $branches = [];
        if (settings()->enable_branch == 1) {
            $branches = Branch::all();
        }

        return view('shopboss::sale.edit', compact('sale', 'customers', 'branches'));
    }

    public function update(Request $request, $id)
    {
        try
        {
            $req = $request->all();
            $sale = Sale::with('saleDetails.product')->find($id);
            
            // Check branch access if enabled
            if (settings()->enable_branch == 1) {
                if (Auth::user()->branch && $sale->branch_id != Auth::user()->branch->id) {
                    return redirect()->route('sales.index')
                        ->withErrors('You do not have access to update this sale.');
                }
            }

            DB::beginTransaction();
            
            // Use branch_id from request if branch system is enabled, otherwise use current
            $branch_id = settings()->enable_branch == 1 ? Auth::user()->branch->id :null;

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
                    'branch_id'               => $branch_id,
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
                    $purchase_detail_query = PurchaseDetail::query()
                            ->where('product_id', $product->id)
                            ->where('available_qty', '>', 0.0001)
                            ->where('available_qty', '>=', $item['qty'])
                            ->orderBy('created_at');
                    
                    // Filter purchase details by branch if branch system is enabled
                    if (settings()->enable_branch == 1) {
                        $purchase_detail_query->where('branch_id', $branch_id);
                    }
                    
                    $purchase_detail = $purchase_detail_query->first();

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
                'branch_id'           => $branch_id,
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
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $sale->branch_id != Auth::user()->branch->id) {
                return redirect()->route('sales.index')
                    ->withErrors('You do not have access to delete this sale.');
            }
        }
        
        $sale->salePayments()->delete();
        $sale->saleDetails()->delete();
        $sale->delete();

        return redirect()->route('sales.index')->withSuccess(__('Sale Deleted'));
    }
}
