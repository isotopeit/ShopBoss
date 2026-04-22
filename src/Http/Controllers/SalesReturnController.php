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
use Isotope\ShopBoss\Models\SaleDetails;
use Isotope\ShopBoss\Models\SaleReturn;
use Isotope\ShopBoss\Models\SaleReturnDetail;
use Isotope\ShopBoss\Models\SaleReturnPayment;
use Isotope\ShopBoss\Observers\SaleReturnObserver;

class SalesReturnController extends Controller
{
    public static $permissions = [
        'index'   => ['access_sale_returns', 'Sale Return List'],
        'create'  => ['create_sale_returns', 'Sale Return Create'],
        'store'   => ['store_sale_returns', 'Sale Return Store'],
        'show'    => ['show_sale_returns', 'Sale Return Store'],
        'edit'    => ['edit_sale_returns', 'Sale Return Edit'],
        'update'  => ['update_sale_returns', 'Sale Return Update'],
        'destroy' => ['delete_sale_returns', 'Sale Return Delete'],
    ];

    public function pdf($id)
    {
        $saleReturn = SaleReturn::findOrFail($id);
        
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $saleReturn->branch_id != Auth::user()->branch->id) {
                return redirect()->route('sale-returns.index')
                    ->withErrors('You do not have access to view this sale return PDF.');
            }
        }
        
        $customer = Customer::findOrFail($saleReturn->customer_id);

        $pdf = \PDF::loadview('pos::print', [
            'sale_return' => $saleReturn,
            'customer' => $customer,
        ])->setPaper('a4');

        return $pdf->stream('sale-return-'. $saleReturn->reference .'.pdf');
    }

    public function index() {
        $query = SaleReturn::search();
        
        // Filter by branch if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch) {
                $query->where('branch_id', Auth::user()->branch->id);
            }
        }
        
        $sale_returns = $query->latest()->paginate(15);
        
        // Get branches for dropdown
        $branches = [];
        if (settings()->enable_branch == 1) {
            $branches = Branch::all();
        }
        
        return view('shopboss::salesreturn.index', compact('sale_returns', 'branches'));
    }

    public function create() 
    {
        // Get customers
        $query = Customer::selectRaw('id, customer_name as text, customer_phone as subText');
        
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
        
        return view('shopboss::salesreturn.create', compact('customers', 'branches','paymentMethods', 'banks'));
    }

    public function store(Request $request) {
        try {
            // dd($request->all());
            $req = $request->all();
            $products = [];
            DB::beginTransaction();
            
            // Set branch_id with a proper default
            $branch_id = null; // Default branch ID
            
            // If branch system is enabled, get branch_id from request or user
            if (settings()->enable_branch == 1) {
                if (isset($req['branch_id']) && !empty($req['branch_id'])) {
                    $branch_id = $req['branch_id'];
                } elseif (Auth::user()->branch) {
                    $branch_id = Auth::user()->branch->id;
                }
            }
            
            foreach ($req['products'] as $item) {
                $saleDetail = SaleDetails::with('sale')->findOrFail($item['product_id']);
                
                // Check if sale detail belongs to the selected branch
                if (settings()->enable_branch == 1) {
                    if ($saleDetail->branch_id != $branch_id) {
                        throw new Exception("Sale detail does not belong to the selected branch", 403);
                    }
                }
                
                array_push($products, [
                    'branch_id'      => $branch_id,
                    'product_id'     => $saleDetail->product_id,
                    'product_name'   => $saleDetail->product->product_name,
                    'product_code'   => $saleDetail->product->product_code,
                    'sale_id'        => $saleDetail->sale_id,
                    'sale_detail_id' => $saleDetail->id,
                    'unit_price'     => $saleDetail->unit_price,
                    'quantity'       => $item['qty'],
                    'sub_total'      => $item['qty'] * $saleDetail->unit_price,
                ]);
                
                // Filter purchase details by branch if needed
                $purchase_detail_query = PurchaseDetail::query()->where('id', $saleDetail->purchase_detail_id);
                
                if (settings()->enable_branch == 1) {
                    $purchase_detail_query->where('branch_id', $branch_id);
                }
                
                $purchase_detail = $purchase_detail_query->first();
                
                if (!$purchase_detail) {
                    throw new Exception("Purchase detail not found for the selected branch", 404);
                }

                $purchase_detail->update([
                    'sale_qty'      => $purchase_detail->sale_qty - $item['qty'],
                    'available_qty' => $purchase_detail->available_qty + $item['qty'],
                ]);
                
                $saleDetail->increment('return_qty', $item['qty']);
            }

            $customer = Customer::find($req['customer_id']);
            if(is_null($customer)) throw new Exception("Customer not found", 404);

            $totalSubTotal = collect($products)->sum('sub_total');
            $payload = [
                'date'               => $req['date'],
                'branch_id'          => $branch_id,
                'sale_id'            => $saleDetail->sale_id,
                'customer_id'        => $customer->id,
                'customer_name'      => $customer->customer_name,
                'paid_amount'        => $req['paid_amount'],
                'payment_method'     => $req['payment_method_id'],
                'note'               => $req['note'],
            ];
            
            $payload['total_amount'] = $totalSubTotal;
            $payload['due_amount']   = $payload['total_amount'] - $payload['paid_amount'];

            if ($payload['due_amount'] == $payload['total_amount']) {
                $payment_status = 'Unpaid';
            } elseif ($payload['due_amount'] > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }
            $payload['payment_status'] = $payment_status;
            $saleReturn = SaleReturn::create($payload);

            if ($payload['paid_amount'] > 0) {
                SaleReturnPayment::create([
                    'date'           => $req['date'],
                    'reference'      => 'INV/' . $saleReturn->reference,
                    'amount'         => $saleReturn->paid_amount,
                    'sale_return_id' => $saleReturn->id,
                    'payment_method' => $req['payment_method_id'],
                    'branch_id'      => $branch_id,
                ]);
            }
            foreach ($products as $product) {
                SaleReturnDetail::create(array_merge([
                    'sale_return_id' => $saleReturn->id
                ], $product));
            }

            $saleReturnObserver = new SaleReturnObserver();
            $saleReturnObserver->created($saleReturn);
            // Only handle bank transaction if payment method is bank
            $paymentMethod = FinanceParticular::find($req['payment_method_id'] ?? null);

            if (str_contains($paymentMethod->alias, 'bank') && !isset($req['bank_id']))
                throw new Exception("Bank is required for bank payment method", 400);

            if ($paymentMethod && str_contains($paymentMethod->alias, 'bank')) {
                $this->handleBankTransaction($req, $saleReturn, $paymentMethod);
            }

            DB::commit();
            return redirect()->route('sale-returns.index')->withSuccess(__('Sale Return Successfull'));

        } catch (Exception $e) {
            dd($e);
            DB::rollBack();
            return redirect()->route('sale-returns.index')->withSuccess(__($e->getMessage()));
        }
    }

    private function handleBankTransaction($req, $saleReturn, $paymentMethod)
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

        $description = "Payment of Create Sale : {$saleReturn->reference} | Bank({$bank->name}:***" . substr($bank->account_number, -4) . ")";

        // Finance record তৈরি
        $financeRecordId = null;
        if (class_exists(FinanceRecord::class) && class_exists(FinanceParticular::class)) {
            $bankParticular = $paymentMethod;

            if ($bankParticular) {
                $operation = (!empty($req['paid_amount']) && $req['paid_amount'] > 0) ? 'increment' : 'decrement';
                $financeRecord = FinanceRecord::entry([
                    'description'     => $description,
                    'amount'          => $amount,
                    'reference_no'    => $saleReturn->reference,
                    'recordable_type' => SaleReturn::class,
                    'recordable_id'   => $saleReturn->id,
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
            'invoice_id'        => $saleReturn->id,
        ]);
    }


    public function show($id) 
    {
        $sale_return = SaleReturn::find($id);
        
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $sale_return->branch_id != Auth::user()->branch->id) {
                return redirect()->route('sale-returns.index')
                    ->withErrors('You do not have access to view this sale return.');
            }
        }
        
        $customer = Customer::findOrFail($sale_return->customer_id);

        return view('shopboss::salesreturn.show', compact('sale_return', 'customer'));
    }

    public function edit($id) 
    {
        $sale_return = SaleReturn::find($id);
        
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $sale_return->branch_id != Auth::user()->branch->id) {
                return redirect()->route('sale-returns.index')
                    ->withErrors('You do not have access to edit this sale return.');
            }
        }
        
        // Get customers
        $query = Customer::selectRaw('id, customer_name as text, customer_phone as subText');
        
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
        
        return view('shopboss::salesreturn.edit', compact('sale_return', 'customers', 'branches'));
    }

    public function update(Request $request, $id) {
        try {
            $req = $request->all();
            $saleReturn = SaleReturn::find($id);
            
            // Check branch access if enabled
            if (settings()->enable_branch == 1) {
                if (Auth::user()->branch && $saleReturn->branch_id != Auth::user()->branch->id) {
                    return redirect()->route('sale-returns.index')
                        ->withErrors('You do not have access to update this sale return.');
                }
            }
            
            // Set branch_id with a proper default
            $branch_id = $saleReturn->branch_id; // Default to current branch_id
            
            // If branch system is enabled, get branch_id from request or user
            if (settings()->enable_branch == 1) {
                if (isset($req['branch_id']) && !empty($req['branch_id'])) {
                    $branch_id = $req['branch_id'];
                } elseif (Auth::user()->branch) {
                    $branch_id = Auth::user()->branch->id;
                }
            }

            DB::beginTransaction();
            foreach ($req['products'] as $item) {
                $saleReturnDetail = SaleReturnDetail::findOrFail($item['product_id']);
                $product = Product::find($saleReturnDetail->product_id);
                $payload = [
                    'unit_price'     => $saleReturnDetail->unit_price,
                    'quantity'       => $item['qty'],
                    'sub_total'      => $item['qty'] * $saleReturnDetail->unit_price,
                    'branch_id'      => $branch_id,
                ];
                $saleDetail = SaleDetails::find($saleReturnDetail->sale_detail_id);
                
                // Filter purchase details by branch if needed
                $purchase_detail_query = PurchaseDetail::query()->where('id', $saleDetail->purchase_detail_id);
                
                if (settings()->enable_branch == 1) {
                    $purchase_detail_query->where('branch_id', $branch_id);
                }
                
                $purchase_detail = $purchase_detail_query->first();
                
                if (!$purchase_detail) {
                    throw new Exception("Purchase detail not found for the selected branch", 404);
                }

                $purchase_detail->update([
                    'sale_qty'      => ($purchase_detail->sale_qty + $saleReturnDetail->quantity) - $item['qty'],
                    'available_qty' => ($purchase_detail->available_qty - $saleReturnDetail->quantity) + $item['qty'],
                ]);
                
                $saleDetail->update(['return_qty' => ($saleDetail->return_qty - $saleReturnDetail->quantity) + $item['qty'] ]);
                $saleReturnDetail->update($payload);
            }

            $totalSubTotal = $saleReturn->saleReturnDetails->sum('sub_total');
            $payload = [
                'date'               => $req['date'],
                'note'               => $req['note'],
                'branch_id'          => $branch_id,
            ];
            
            $payload['total_amount'] = $totalSubTotal;
            $payload['due_amount']   = $payload['total_amount'] - $saleReturn->paid_amount;

            if ($payload['due_amount'] == $payload['total_amount']) {
                $payment_status = 'Unpaid';
            } elseif ($payload['due_amount'] > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }
            $payload['payment_status'] = $payment_status;
            $saleReturn->update($payload);

            DB::commit();
            return redirect()->route('sale-returns.index')->withSuccess("Sale Return Updated");
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('sale-returns.index')->withErrors($e->getMessage());
        }
    }
    
    public function destroy($id) {
        $saleReturn = SaleReturn::with('saleReturnDetails', 'saleReturnPayments')->findOrFail($id);
        
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $saleReturn->branch_id != Auth::user()->branch->id) {
                return redirect()->route('sale-returns.index')
                    ->withErrors('You do not have access to delete this sale return.');
            }
        }

        foreach ($saleReturn->saleReturnDetails as $saleReturnDetails) 
        {
            $saleReturnDetails->saleDetails->decrement('return_qty', $saleReturnDetails->quantity);
        }

        $saleReturn->saleReturnDetails()->delete();
        $saleReturn->saleReturnPayments()->delete();
        $saleReturn->delete();
        return redirect()->route('sale-returns.index')->withSuccess("Sale Return deleted");
    }
}
