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
use Isotope\ShopBoss\Models\Product;
use Isotope\ShopBoss\Models\Purchase;
use Isotope\ShopBoss\Models\PurchaseDetail;
use Isotope\ShopBoss\Models\PurchasePayment;
use Isotope\ShopBoss\Models\Supplier;
use Isotope\ShopBoss\Observers\PurchaseObserver;

class PurchaseController extends Controller
{
    public static $permissions = [
        'index'   => ['access_purchases', 'Purchase List'],
        'create'  => ['create_purchases', 'Purchase Create'],
        'store'   => ['store_purchases', 'Purchase Store'],
        'show'    => ['show_purchases', 'Purchase Show'],
        'edit'    => ['edit_purchases', 'Purchase Edit'],
        'update'  => ['update_purchases', 'Purchase Update'],
        'destroy' => ['delete_purchases', 'Purchase Delete'],
    ];

    public function index()
    {
        $query = Purchase::search();
        
        // Filter by branch if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch) {
                $query->where('branch_id', Auth::user()->branch->id);
            }
        }
        
        $purchases = $query->orderByDesc('id')->paginate(15);
        
        // Get branches for dropdown
        $branches = [];
        if (settings()->enable_branch == 1) {
            $branches = Branch::all();
        }
        
        return view('shopboss::purchase.index', compact('purchases', 'branches'));
    }

    public function create()
    {
        // Get suppliers
        $query = Supplier::selectRaw("id, supplier_name as text, supplier_phone as subText");
        
        // Filter suppliers by branch if enabled
        if (settings()->enable_branch == 1 && Auth::user()->branch) {
            $query->where('branch_id', Auth::user()->branch->id);
        }
        
        $suppliers = $query->get();
        
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
        
        return view('shopboss::purchase.create', compact('suppliers', 'branches', 'paymentMethods', 'banks'));
    }


    public function store(Request $request)
    {
        try {
            $req = $request->all();
            DB::beginTransaction();

            $products = [];
            
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

            foreach ($req['products'] as $item) {
                $product  = Product::find($item['product_id']);
                $discount = array_key_exists('percentage', $item) ? ($product->product_cost / 100) * floatval($item['discount']) : $item['discount'];
                array_push($products, [
                    'branch_id'               => $branch_id,
                    'product_id'              => $product->id,
                    'product_name'            => $product->product_name,
                    'product_code'            => $product->product_code,
                    'purchase_qty'            => $item['qty'],
                    'available_qty'           => $item['qty'],
                    'unit_price'              => $product->product_cost,
                    'sub_total'               => ($product->product_cost - $discount) * floatval($item['qty']),
                    'product_discount_amount' => $discount,
                    'product_tax_amount'      => 0,
                ]);
            }

            $supplier = Supplier::find($req['supplier_id']);
            if(is_null($supplier)) throw new Exception("Supplier not found", 404);

            $totalSubTotal = collect($products)->sum('sub_total');
            $payload = [
                'branch_id'           => $branch_id,
                'supplier_id'         => $supplier->id,
                'supplier_name'       => $supplier->supplier_name,
                'date'                => $req['date'],
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
            $purchase = Purchase::create($payload);

            if ($payload['paid_amount'] > 0) {
                PurchasePayment::create([
                    'date'           => $req['date'],
                    'reference'      => 'INV/' . $purchase->reference,
                    'amount'         => $purchase->paid_amount,
                    'purchase_id'    => $purchase->id,
                    'payment_method' => $req['payment_method_id'],
                    'branch_id'      => $branch_id,
                ]);
            }
            foreach ($products as $product) {
                PurchaseDetail::create(array_merge([
                    'purchase_id' => $purchase->id
                ], $product));
            }


            $purchaseObserver = new PurchaseObserver();
            $purchaseObserver->created($purchase);
             // Only handle bank transaction if payment method is bank
            $paymentMethod = FinanceParticular::find($req['payment_method_id'] ?? null);
            
            if(str_contains($paymentMethod->alias , 'bank') && !isset($req['bank_id']))
                throw new Exception("Bank is required for bank payment method", 400);

            if ($paymentMethod && str_contains($paymentMethod->alias , 'bank')) {
                $this->handleBankTransaction($req, $purchase,$paymentMethod);
            }

            DB::commit();
            return redirect()->route('purchases.index')->withSuccess("Purchase Created");
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('purchases.index')->withErrors($e->getMessage());
        }
    }


    private function handleBankTransaction($req, $purchase,$paymentMethod)
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

        $description = "Payment of Create Purchase : {$purchase->reference} | Bank({$bank->name}:***" . substr($bank->account_number, -4) . ")";

        // Finance record তৈরি
        $financeRecordId = null;
        if (class_exists(FinanceRecord::class) && class_exists(FinanceParticular::class)) {
            $bankParticular = $paymentMethod;

            if ($bankParticular) {
                $operation = (!empty($req['paid_amount']) && $req['paid_amount'] > 0) ? 'increment' : 'decrement';
                $financeRecord = FinanceRecord::entry([
                    'description'     => $description,
                    'amount'          => $amount,
                    'reference_no'    => $purchase->reference,
                    'recordable_type' => Purchase::class,
                    'recordable_id'   => $purchase->id,
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
            'invoice_id'        => $purchase->id,
        ]);
    }


    public function show($id)
    {
        $purchase = Purchase::with('supplier', 'purchaseDetails')->findOrFail($id);
        
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $purchase->branch_id != Auth::user()->branch->id) {
                return redirect()->route('purchases.index')
                    ->withErrors('You do not have access to view this purchase.');
            }
        }
        
        return view('shopboss::purchase.show', compact('purchase'));
    }


    public function edit($id)
    {
        $purchase = Purchase::with('purchaseDetails.product')->find($id);
        
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $purchase->branch_id != Auth::user()->branch->id) {
                return redirect()->route('purchases.index')
                    ->withErrors('You do not have access to edit this purchase.');
            }
        }
        
        // Get suppliers
        $query = Supplier::selectRaw("id, supplier_name as text, supplier_phone as subText");
        
        // Filter suppliers by branch if enabled
        if (settings()->enable_branch == 1 && Auth::user()->branch) {
            $query->where('branch_id', Auth::user()->branch->id);
        }
        
        $suppliers = $query->get();
        
        // Get branches for dropdown
        $branches = [];
        if (settings()->enable_branch == 1) {
            $branches = Branch::all();
        }
        
        return view('shopboss::purchase.edit', compact('purchase', 'suppliers', 'branches'));
    }


    public function update(Request $request, $id)
    {
        try
        {
            $req = $request->all();
            $purchase = Purchase::with('purchaseDetails.product')->find($id);
            
            // Check branch access if enabled
            if (settings()->enable_branch == 1) {
                if (Auth::user()->branch && $purchase->branch_id != Auth::user()->branch->id) {
                    return redirect()->route('purchases.index')
                        ->withErrors('You do not have access to update this purchase.');
                }
            }

            DB::beginTransaction();
            
            // Set branch_id with a proper default
            $branch_id = $purchase->branch_id; // Default to current branch_id
            
            // If branch system is enabled, get branch_id from request or user
            if (settings()->enable_branch == 1) {
                if (isset($req['branch_id']) && !empty($req['branch_id'])) {
                    $branch_id = $req['branch_id'];
                } elseif (Auth::user()->branch) {
                    $branch_id = Auth::user()->branch->id;
                }
            }

            foreach ($req['products'] as $item) {
                $product  = Product::find($item['product_id']);
                $discount = array_key_exists('percentage', $item) ? ($product->product_cost / 100) * floatval($item['discount']) : $item['discount'];
                $payload  = [
                    'product_id'              => $product->id,
                    'product_name'            => $product->product_name,
                    'product_code'            => $product->product_code,
                    'purchase_qty'            => $item['qty'],
                    'available_qty'           => $item['qty'],
                    'unit_price'              => $product->product_cost,
                    'sub_total'               => ($product->product_cost - $discount) * floatval($item['qty']),
                    'product_discount_amount' => $discount,
                    'product_tax_amount'      => 0,
                    'branch_id'               => $branch_id,
                ];

                if (array_key_exists('detail_id', $item)) {
                    $detail = PurchaseDetail::find($item['detail_id']);
                    $detail->update($payload);
                } else {
                    $purchase->purchaseDetails()->create($payload);
                }
            }

            $supplier = Supplier::findOrFail($request->supplier_id);
            if(is_null($supplier)) throw new Exception("Supplier not found", 404);
            $purchase->refresh();

            $totalSubTotal = $purchase->purchaseDetails->sum('sub_total');
            $payload = [
                'supplier_id'         => $supplier->id,
                'supplier_name'       => $supplier->supplier_name,
                'date'                => $req['date'],
                'tax_percentage'      => $req['tax_percentage'],
                'tax_amount'          => ($totalSubTotal / 100) * $req['tax_percentage'],
                'discount_percentage' => (100/$totalSubTotal)*$req['discount_amount'],
                'discount_amount'     => $req['discount_amount'],
                'note'                => $req['note'],
                'shipping_amount'     => $req['shipping_amount'],
                'total_amount'        => $totalSubTotal + $req['shipping_amount'] + $purchase->tax_amount - $req['discount_amount'],
                'branch_id'           => $branch_id,
            ];

            $payload['due_amount'] = $payload['total_amount'] - $purchase->paid_amount;
            if ($payload['due_amount'] == $payload['total_amount']) {
                $payment_status = 'Unpaid';
            } elseif ($payload['due_amount'] > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }
            $payload['payment_status'] = $payment_status;

            $purchase->update($payload);

            DB::commit();
            return redirect()->route('purchases.index')->withSuccess("Purchase Updated");
        }
        catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('purchases.index')->withErrors($e->getMessage());
        }
    }


    public function destroy($id)
    {
        $purchase = Purchase::with('purchaseDetails', 'purchasePayments', 'purchaseReturns')->findOrFail($id);
        
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $purchase->branch_id != Auth::user()->branch->id) {
                return redirect()->route('purchases.index')
                    ->withErrors('You do not have access to delete this purchase.');
            }
        }
        
        $purchase->purchaseDetails()->delete();
        $purchase->purchasePayments()->delete();
        $purchase->delete();
        return redirect()->route('purchases.index')->withSuccess("Purchase deleted");
    }

    public function pdf($id)
    {
        $purchase = Purchase::findOrFail($id);
        
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $purchase->branch_id != Auth::user()->branch->id) {
                return redirect()->route('purchases.index')
                    ->withErrors('You do not have access to view this purchase PDF.');
            }
        }
        
        $supplier = Supplier::findOrFail($purchase->supplier_id);

        $pdf = \PDF::loadView('shopboss::purchase.print', [
            'purchase' => $purchase,
            'supplier' => $supplier,
        ])->setPaper('a4');

        return $pdf->stream('purchase-' . $purchase->reference . '.pdf');
    }
}
