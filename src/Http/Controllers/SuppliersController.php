<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Illuminate\Http\Request;
use Isotope\ShopBoss\Models\Branch;
use Isotope\ShopBoss\Models\Supplier;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class SuppliersController extends Controller
{
    public static $permissions = [
        'index'           => ['access_suppliers', 'Supplier List'],
        'create'          => ['create_suppliers', 'Supplier Create'],
        'store'           => ['store_suppliers', 'Supplier Store'],
        'edit'            => ['edit_suppliers', 'Supplier Edit'],
        'update'          => ['update_suppliers', 'Supplier Update'],
        'destroy'         => ['delete_suppliers', 'Supplier Delete'],
        'supplierSelecr2' => ['supplierSelecr2', 'Supplier Select List'],
    ];

    public function index() {
        $query = Supplier::search();
        
        // Filter by branch if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch) {
                $query->where('branch_id', Auth::user()->branch->id);
            }
        }
        
        $suppliers = $query->orderByDesc('id')->paginate(15);
        
        // Get branches for dropdown
        $branches = [];
        if (settings()->enable_branch == 1) {
            $branches = Branch::all();
        }
        
        return view('shopboss::people.suppliers.index', compact('suppliers', 'branches'));
    }

    public function store(Request $request) {
        $rules = [
            'supplier_name'  => 'required|string|max:255',
            'supplier_phone' => 'required|max:15|min:11',
            'supplier_email' => 'nullable|email|max:255',
            'city'           => 'nullable|string|max:255',
            'country'        => 'nullable|string|max:255',
            'company_name'   => 'nullable|string|max:255',
            'address'        => 'nullable|string|max:500',
        ];
        
        // Add branch validation if enabled
        if (settings()->enable_branch == 1) {
            $rules['branch_id'] = 'required|exists:branches,id';
        }
        
        $request->validate($rules);
        
        $data = [
            'supplier_name'  => $request->supplier_name,
            'supplier_phone' => $request->supplier_phone,
            'supplier_email' => $request->supplier_email,
            'company_name'   => $request->company_name,
            'city'           => $request->city,
            'country'        => $request->country,
            'address'        => $request->address
        ];
        
        // Add branch ID if branch system is enabled
        if (settings()->enable_branch == 1) {
            $data['branch_id'] = $request->branch_id;
        }

        Supplier::create($data);

        return redirect()->route('suppliers.index')->withSuccess('Supplier Created!');
    }

    public function show(Supplier $supplier)
    {
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $supplier->branch_id != Auth::user()->branch->id) {
                return redirect()->route('suppliers.index')
                    ->withErrors('You do not have access to view this supplier.');
            }
        }
        
        return view('shopboss::people.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $supplier->branch_id != Auth::user()->branch->id) {
                return redirect()->route('suppliers.index')
                    ->withErrors('You do not have access to edit this supplier.');
            }
        }
        
        // Get branches for dropdown
        $branches = [];
        if (settings()->enable_branch == 1) {
            $branches = Branch::all();
        }
        
        return view('shopboss::people.suppliers.edit', compact('supplier', 'branches'));
    }

    public function update(Request $request, Supplier $supplier) {
        abort_if(Gate::denies('edit_suppliers'), 403);
        
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $supplier->branch_id != Auth::user()->branch->id) {
                return redirect()->route('suppliers.index')
                    ->withErrors('You do not have access to edit this supplier.');
            }
        }

        $rules = [
            'supplier_name'  => 'required|string|max:255',
            'supplier_phone' => 'required|max:15|min:11',
            'supplier_email' => 'nullable|email|max:255',
            'company_name'   => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:255',
            'country'        => 'nullable|string|max:255',
            'address'        => 'nullable|string|max:500',
        ];
        
        // Add branch validation if enabled
        if (settings()->enable_branch == 1) {
            $rules['branch_id'] = 'required|exists:branches,id';
        }
        
        $request->validate($rules);

        $data = [
            'supplier_name'  => $request->supplier_name,
            'supplier_phone' => $request->supplier_phone,
            'supplier_email' => $request->supplier_email,
            'company_name'   => $request->company_name,
            'city'           => $request->city,
            'country'        => $request->country,
            'address'        => $request->address
        ];
        
        // Add branch ID if branch system is enabled
        if (settings()->enable_branch == 1) {
            $data['branch_id'] = $request->branch_id;
        }

        $supplier->update($data);

        return redirect()->route('suppliers.index')->withSuccess('Supplier Updated!');
    }

    public function destroy(Supplier $supplier) {
        abort_if(Gate::denies('delete_suppliers'), 403);
        
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $supplier->branch_id != Auth::user()->branch->id) {
                return redirect()->route('suppliers.index')
                    ->withErrors('You do not have access to delete this supplier.');
            }
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')->withSuccess('Supplier Deleted!');
    }

    public function supplierSelecr2()
    {
        $query = Supplier::query();
        
        // Filter by branch if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch) {
                $query->where('branch_id', Auth::user()->branch->id);
            }
        }
        
        $suppliers = $query->get();

        $data = [];

        foreach ($suppliers as $supplier)
        {
            if (is_null($supplier->company_name))
            {
                $data[] = [
                    'id'      => $supplier->id,
                    'text'    => $supplier->supplier_name,
                    'subText' => $supplier->supplier_phone
                ];
            } else
            {
                $data[] = [
                    'id'      => $supplier->id,
                    'text'    => $supplier->company_name,
                    'subText' => $supplier->supplier_name . '-' . $supplier->supplier_phone
                ];
            }
        }

        return response()->json($data);
    }
}
