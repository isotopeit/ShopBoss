<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Isotope\ShopBoss\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Isotope\ShopBoss\Models\Product;
use Isotope\ShopBoss\Models\Supplier;
use Isotope\ShopBoss\Models\Purchase;
use Isotope\ShopBoss\Models\PurchaseDetail;
use Isotope\ShopBoss\Models\PurchaseReturn;
use Isotope\ShopBoss\Models\PurchaseReturnDetail;
use Isotope\ShopBoss\Models\PurchaseReturnPayment;

class PurchasesReturnController extends Controller
{
    public static $permissions = [
        'index'   => ['access_purchase_returns', 'Purchase Return List'],
        'create'  => ['create_purchase_returns', 'Purchase Return Create'],
        'store'   => ['store_purchase_returns', 'Purchase Return Store'],
        'show'    => ['show_purchase_returns', 'Purchase Return Show'],
        'edit'    => ['edit_purchase_returns', 'Purchase Return Edit'],
        'update'  => ['update_purchase_returns', 'Purchase Return Update'],
        'destroy' => ['delete_purchase_returns', 'Purchase Return Delete'],
    ];

    public function pdf($id)
    {
        $purchaseReturn = PurchaseReturn::findOrFail($id);
        
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $purchaseReturn->branch_id != Auth::user()->branch->id) {
                return redirect()->route('purchase-returns.index')
                    ->withErrors('You do not have access to view this purchase return PDF.');
            }
        }
        
        $supplier = Supplier::findOrFail($purchaseReturn->supplier_id);

        $pdf = \PDF::loadview('pos::print', [
            'purchase_return' => $purchaseReturn,
            'supplier' => $supplier,
        ])->setPaper('a4');

        return $pdf->stream('purchase-return-'. $purchaseReturn->reference .'.pdf');
    }

    public function index() 
    {
        $query = PurchaseReturn::search();
        
        // Filter by branch if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch) {
                $query->where('branch_id', Auth::user()->branch->id);
            }
        }
        
        $purchase_returns = $query->latest()->paginate(15);
        
       
        
        return view('shopboss::purchases-return.index', compact('purchase_returns'));
    }

    public function create() 
    {
        // Get suppliers
        $query = Supplier::selectRaw('id, supplier_name as text, supplier_phone as subText');
        
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
        
        return view('shopboss::purchases-return.create', compact('suppliers', 'branches'));
    }

    public function store(Request $request) 
    {
        try {
            $req = $request->all();

            $products = [];
            DB::beginTransaction();
            
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
                $purchaseDetail = PurchaseDetail::with('purchase')->findOrFail($item['product_id']);
                
                // Check if purchase detail belongs to the selected branch
                if (settings()->enable_branch == 1) {
                    if ($purchaseDetail->branch_id != $branch_id) {
                        throw new Exception("Purchase detail does not belong to the selected branch", 403);
                    }
                }
                
                array_push($products, [
                    'branch_id'         => $branch_id,
                    'product_id'        => $purchaseDetail->product_id,
                    // 'product_name'      => $purchaseDetail->product_name,
                    'product_code'      => $purchaseDetail->product_code,
                    'purchase_id'       => $purchaseDetail->purchase_id,
                    'purchase_detail_id'=> $purchaseDetail->id,
                    'unit_price'        => $purchaseDetail->unit_price,
                    'quantity'          => $item['qty'],
                    'sub_total'         => $item['qty'] * $purchaseDetail->unit_price,
                ]);

                $purchaseDetail->update([
                    'available_qty' => $purchaseDetail->available_qty - $item['qty'],
                ]);
            }

            $supplier = Supplier::find($req['supplier_id']);
            if(is_null($supplier)) throw new Exception("Supplier not found", 404);

            $totalSubTotal = collect($products)->sum('sub_total');
            $payload = [
                'date'               => $req['date'],
                'branch_id'          => $branch_id,
                'purchase_id'        => $purchaseDetail->purchase_id,
                'supplier_id'        => $supplier->id,
                'supplier_name'      => $supplier->supplier_name,
                'paid_amount'        => $req['paid_amount'],
                'payment_method'     => $req['payment_method'],
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
            $purchaseReturn = PurchaseReturn::create($payload);

            if ($payload['paid_amount'] > 0) {
                PurchaseReturnPayment::create([
                    'date'               => $req['date'],
                    'reference'          => 'INV/' . $purchaseReturn->reference,
                    'amount'             => $purchaseReturn->paid_amount,
                    'purchase_return_id' => $purchaseReturn->id,
                    'payment_method'     => $req['payment_method'],
                    'branch_id'          => $branch_id,
                ]);
            }
            foreach ($products as $product) {
                PurchaseReturnDetail::create(array_merge([
                    'purchase_return_id' => $purchaseReturn->id,
                    'branch_id'=> settings()->enable_branch == 1 ? Auth::user()->branch->id : null,
                ], $product));
            }
            DB::commit();
            return redirect()->route('purchase-returns.index')->withSuccess(__('Purchase Return Successfull'));

        } catch (Exception $th) {
            dd($th);
            DB::rollBack();
            return redirect()->route('purchase-returns.index')->withSuccess(__($th->getMessage()));
        }
    }

    public function show($id) 
    {
        $purchaseReturn = PurchaseReturn::find($id);
        
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $purchaseReturn->branch_id != Auth::user()->branch->id) {
                return redirect()->route('purchase-returns.index')
                    ->withErrors('You do not have access to view this purchase return.');
            }
        }
        
        $supplier = Supplier::findOrFail($purchaseReturn->supplier_id);

        return view('shopboss::purchases-return.show', compact('purchaseReturn', 'supplier'));
    }

    public function edit($id) 
    {
        $purchase_return = PurchaseReturn::find($id);
        
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $purchase_return->branch_id != Auth::user()->branch->id) {
                return redirect()->route('purchase-returns.index')
                    ->withErrors('You do not have access to edit this purchase return.');
            }
        }
        
        // Get suppliers
        $query = Supplier::selectRaw('id, supplier_name as text, supplier_phone as subText');
        
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
        
        return view('shopboss::purchases-return.edit', compact('purchase_return', 'suppliers', 'branches'));
    }

    public function update(Request $request, $id) 
    {
        try {
            $req = $request->all();
            $purchaseReturn = PurchaseReturn::find($id);
            
            // Check branch access if enabled
            if (settings()->enable_branch == 1) {
                if (Auth::user()->branch && $purchaseReturn->branch_id != Auth::user()->branch->id) {
                    return redirect()->route('purchase-returns.index')
                        ->withErrors('You do not have access to update this purchase return.');
                }
            }
            
            // Set branch_id with a proper default
            $branch_id = $purchaseReturn->branch_id; // Default to current branch_id
            
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
                $purchaseReturnDetail = PurchaseReturnDetail::findOrFail($item['product_id']);
                $product = Product::find($purchaseReturnDetail->product_id);
                $payload = [
                    'unit_price'     => $purchaseReturnDetail->unit_price,
                    'quantity'       => $item['qty'],
                    'sub_total'      => $item['qty'] * $purchaseReturnDetail->unit_price,
                    'branch_id'      => $branch_id,
                ];
                $purchaseDetail = PurchaseDetail::find($purchaseReturnDetail->purchase_detail_id);
                
                // Filter purchase details by branch if needed
                if (settings()->enable_branch == 1) {
                    if ($purchaseDetail->branch_id != $branch_id) {
                        throw new Exception("Purchase detail does not belong to the selected branch", 403);
                    }
                }

                $purchaseDetail->update([
                    'available_qty' => ($purchaseDetail->available_qty + $purchaseReturnDetail->quantity) - $item['qty'],
                ]);
                $purchaseReturnDetail->update($payload);
            }

            $totalSubTotal = $purchaseReturn->purchaseReturnDetails->sum('sub_total');
            $payload = [
                'date'               => $req['date'],
                'note'               => $req['note'],
                'branch_id'          => $branch_id,
            ];
            
            $payload['total_amount'] = $totalSubTotal;
            $payload['due_amount']   = $payload['total_amount'] - $purchaseReturn->paid_amount;

            if ($payload['due_amount'] == $payload['total_amount']) {
                $payment_status = 'Unpaid';
            } elseif ($payload['due_amount'] > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }
            $payload['payment_status'] = $payment_status;
            $purchaseReturn->update($payload);

            DB::commit();
            return redirect()->route('purchase-returns.index')->withSuccess("Purchase Return Updated");
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('purchase-returns.index')->withErrors($e->getMessage());
        }
    }
    
    public function destroy($id) 
    {
        $purchaseReturn = PurchaseReturn::with('purchaseReturnDetails', 'purchaseReturnPayments')->findOrFail($id);
        
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $purchaseReturn->branch_id != Auth::user()->branch->id) {
                return redirect()->route('purchase-returns.index')
                    ->withErrors('You do not have access to delete this purchase return.');
            }
        }
        
        foreach ($purchaseReturn->purchaseReturnDetails as $purchaseReturnDetails) 
        {
            $purchaseDetail = PurchaseDetail::find($purchaseReturnDetails->purchase_detail_id);
            $purchaseDetail->increment('available_qty', $purchaseReturnDetails->quantity);
        }

        $purchaseReturn->purchaseReturnDetails()->delete();
        $purchaseReturn->purchaseReturnPayments()->delete();
        $purchaseReturn->delete();
        return redirect()->route('purchase-returns.index')->withSuccess("Purchase Return deleted");
    }

    public function purchaseData($id)
    {
        $purchase = Purchase::with(['purchaseDetails.product'])->find($id);

        if (!$purchase) {
            return response()->json([
                'msg' => 'Purchase not found'
            ], 404);
        }

        return response()->json([
            'purchase_details' => $purchase->purchaseDetails->map(function ($detail) {
                return [
                    'id'            => $detail->product_id,
                    'product_name'  => $detail->product_name ?? $detail->product->name ?? '',
                    'product_code'  => $detail->product_code ?? $detail->product->code ?? '',
                    'unit_price'    => $detail->unit_price,
                    'purchase_qty'  => $detail->purchase_qty,
                    'quantity'      => $detail->available_qty ?? $detail->purchase_qty,
                    'sub_total'     => $detail->unit_price * $detail->purchase_qty,
                    'product' => [
                        'uom' => $detail->product->uom ?? '',
                    ]
                ];
            })
        ]);
    }
}
