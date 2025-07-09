<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Illuminate\Http\Request;
use Isotope\ShopBoss\Models\Customer;
use Isotope\ShopBoss\Models\Branch;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Support\Renderable;
use Isotope\ShopBoss\Http\Services\DataTables\CustomersDataTable;

class CustomersController extends Controller
{
    public static $permissions = [
        'index'           => ['access_customers', 'Customer List'],
        'create'          => ['create_customers', 'Customer Create'],
        'store'           => ['store_customers', 'Customer Store'],
        'show'            => ['show_customers', 'Customer Show'],
        'edit'            => ['edit_customers', 'Customer Edit'],
        'update'          => ['update_customers', 'Customer Update'],
        'destroy'         => ['delete_customers', 'Customer Delete'],
        'customerSelecr2' => ['customer_select2', 'Customer API'],
        'customerStore'   => ['customer_store', 'Customer API Store'],
    ];

    public function index() 
    {
        $query = Customer::search();
        
        // Filter by branch if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch) {
                $query->where('branch_id', Auth::user()->branch->id);
            }
        }
        
        $customers = $query->orderByDesc('id')->paginate(15);
        
        // Get branches for dropdown
        $branches = [];
        if (settings()->enable_branch == 1) {
            $branches = Branch::all();
        }
        
        return view('shopboss::people.customers.index', compact('customers', 'branches'));
    }

    public function create() 
    {
        // Get branches for dropdown
        $branches = [];
        if (settings()->enable_branch == 1) {
            $branches = Branch::all();
        }
        
        return view('shopboss::people.customers.create', compact('branches'));
    }

    public function store(Request $request) {
        $rules = [
            'customer_name'  => 'required|string|max:255',
            'customer_phone' => 'required|max:255',
        ];
        
        // Add branch validation if enabled
        if (settings()->enable_branch == 1) {
            $rules['branch_id'] = 'required|exists:branches,id';
        }
        
        $request->validate($rules);

        $data = [
            'customer_name'  => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email ?? '',
            'city'           => $request->city ?? '',
            'country'        => $request->country ?? '',
            'address'        => $request->address  ?? ''
        ];
        
        // Add branch ID if branch system is enabled
        if (settings()->enable_branch == 1) {
            $data['branch_id'] = $request->branch_id;
        }

        Customer::create($data);

        return redirect()->route('customers.index')->withSuccess('Customer Created!');
    }

    public function show(Customer $customer) 
    {
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $customer->branch_id != Auth::user()->branch->id) {
                return redirect()->route('customers.index')
                    ->withErrors('You do not have access to view this customer.');
            }
        }
        
        return view('shopboss::people.customers.show', compact('customer'));
    }

    public function edit(Customer $customer) 
    {
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $customer->branch_id != Auth::user()->branch->id) {
                return redirect()->route('customers.index')
                    ->withErrors('You do not have access to edit this customer.');
            }
        }
        
        // Get branches for dropdown
        $branches = [];
        if (settings()->enable_branch == 1) {
            $branches = Branch::all();
        }
        
        return view('shopboss::people.customers.edit', compact('customer', 'branches'));
    }

    public function update(Request $request, Customer $customer) 
    {
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $customer->branch_id != Auth::user()->branch->id) {
                return redirect()->route('customers.index')
                    ->withErrors('You do not have access to edit this customer.');
            }
        }
        
        $rules = [
            'customer_name'  => 'required|string|max:255',
            'customer_phone' => 'required|max:255',
        ];
        
        // Add branch validation if enabled
        if (settings()->enable_branch == 1) {
            $rules['branch_id'] = 'required|exists:branches,id';
        }
        
        $request->validate($rules);

        $data = [
            'customer_name'  => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email,
            'city'           => $request->city,
            'country'        => $request->country,
            'address'        => $request->address
        ];
        
        // Add branch ID if branch system is enabled
        if (settings()->enable_branch == 1) {
            $data['branch_id'] = $request->branch_id;
        }

        $customer->update($data);

        return redirect()->route('customers.index')->withSuccess('Customer Updated!');
    }

    public function destroy(Customer $customer) {
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $customer->branch_id != Auth::user()->branch->id) {
                return redirect()->route('customers.index')
                    ->withErrors('You do not have access to delete this customer.');
            }
        }

        $customer->delete();

        return redirect()->route('customers.index')->withSuccess('Customer Deleted!');
    }

    public function customerStore(Request $request)
    {
        $data = [
            'customer_name'  => $request->name,
            'customer_email' => 'N/A',
            'customer_phone' => $request->phone,
            'city'           => 'N/A',
            'country'        => 'N/A',
            'address'        => 'N/A',
        ];
        
        // Add branch ID if branch system is enabled
        if (settings()->enable_branch == 1 && Auth::user()->branch) {
            $data['branch_id'] = Auth::user()->branch->id;
        }
        
        $customer = Customer::create($data);

        return response()->json($customer);
    }

    public function customerSelecr2()
    {
        $query = Customer::selectRaw('id, customer_name as text, customer_phone as subText');
        
        // Filter by branch if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch) {
                $query->where('branch_id', Auth::user()->branch->id);
            }
        }
        
        $customers = $query->get();

        return response()->json($customers);
    }
}
