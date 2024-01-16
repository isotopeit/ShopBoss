<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Illuminate\Http\Request;
use Isotope\ShopBoss\Models\Supplier;
use Illuminate\Routing\Controller;
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
        $suppliers = Supplier::search()->orderBydesc('id')->paginate(15);
        return view('shopboss::people.suppliers.index',compact('suppliers'));
    }

    public function store(Request $request) {

        $request->validate([
            'supplier_name'  => 'required|string|max:255',
            'supplier_phone' => 'required|max:15|min:11',
            'supplier_email' => 'nullable|email|max:255',
            'city'           => 'nullable|string|max:255',
            'country'        => 'nullable|string|max:255',
            'company_name'   => 'nullable|string|max:255',
            'address'        => 'nullable|string|max:500',
        ]);

        Supplier::create([
            'supplier_name'  => $request->supplier_name,
            'supplier_phone' => $request->supplier_phone,
            'supplier_email' => $request->supplier_email,
            'company_name'   => $request->company_name,
            'city'           => $request->city,
            'country'        => $request->country,
            'address'        => $request->address
        ]);

        return redirect()->route('suppliers.index')->withSuccess('Supplier Created!');
    }

    public function show(Supplier $supplier) 
    {
        return view('shopboss::people.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier) 
    {
        return view('shopboss::people.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier) {
        abort_if(Gate::denies('edit_suppliers'), 403);

        $request->validate([
            'supplier_name'  => 'required|string|max:255',
            'supplier_phone' => 'required|max:15|min:11',
            'supplier_email' => 'nullable|email|max:255',
            'company_name'   => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:255',
            'country'        => 'nullable|string|max:255',
            'address'        => 'nullable|string|max:500',
        ]);

        $supplier->update([
            'supplier_name'  => $request->supplier_name,
            'supplier_phone' => $request->supplier_phone,
            'supplier_email' => $request->supplier_email,
            'company_name'   => $request->company_name,
            'city'           => $request->city,
            'country'        => $request->country,
            'address'        => $request->address
        ]);

        toast('Supplier Updated!', 'info');

        return redirect()->route('suppliers.index');
    }

    public function destroy(Supplier $supplier) {
        abort_if(Gate::denies('delete_suppliers'), 403);

        $supplier->delete();

        toast('Supplier Deleted!', 'warning');

        return redirect()->route('suppliers.index');
    }

    public function supplierSelecr2()
    {
        $suppliers = Supplier::all();

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

                                // company_name as text,
                                //     CONCAT(supplier_name, ' (', supplier_phone , ')') as subText

        return response()->json($data);
    }
    
}
