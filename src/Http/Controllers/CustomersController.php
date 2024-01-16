<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Illuminate\Http\Request;
use Isotope\ShopBoss\Models\Customer;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
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
        $customers = Customer::search()->orderBydesc('id')->paginate(15);
        return view('shopboss::people.customers.index',compact('customers'));
    }


    public function create() 
    {
        return view('shopboss::people.customers.create');
    }


    public function store(Request $request) {
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_phone' => 'required|max:255',
        ]);

        Customer::create([
            'customer_name'  => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email ?? '',
            'city'           => $request->city ?? '',
            'country'        => $request->country ?? '',
            'address'        => $request->address  ?? ''
        ]);

        return redirect()->route('customers.index')->withSuccess('Customer Created!');
    }


    public function show(Customer $customer) 
    {
        return view('shopboss::people.customers.show', compact('customer'));
    }


    public function edit(Customer $customer) 
    {
        return view('shopboss::people.customers.edit', compact('customer'));
    }


    public function update(Request $request, Customer $customer) 
    {
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_phone' => 'required|max:255',
        ]);

        $customer->update([
            'customer_name'  => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email,
            'city'           => $request->city,
            'country'        => $request->country,
            'address'        => $request->address
        ]);

        return redirect()->route('customers.index')->withSuccess('Customer Updated!');
    }


    public function destroy(Customer $customer) {

        $customer->delete();

        return redirect()->route('customers.index')->withSuccess('Customer Deleted!');
    }

    public function customerStore(Request $request)
    {
        $customer = Customer::create([
            'customer_name'  => $request->name,
            'customer_email' => 'N/A',
            'customer_phone' => $request->phone,
            'city'           => 'N/A',
            'country'        => 'N/A',
            'address'        => 'N/A',
        ]);

        return response()->json($customer);
    }

    public function customerSelecr2()
    {
        $customers = Customer::selectRaw('id , customer_name as text , customer_phone as subText')->get();

        return response()->json($customers);
    }
}
