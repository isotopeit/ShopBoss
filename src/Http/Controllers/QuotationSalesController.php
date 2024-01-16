<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Illuminate\Http\Request;
use Isotope\ShopBoss\Models\Product;
use Isotope\ShopBoss\Models\Customer;
use Isotope\ShopBoss\Models\Quotation;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Gloudemans\Shoppingcart\Facades\Cart;
use Modules\Quotation\Http\Requests\StoreQuotationSaleRequest;

class QuotationSalesController extends Controller
{
    public static $permissions = [
        'index'   => ['access_quotation_sales', 'Quotation Sales List'],
        'create'  => ['create_quotation_sales', 'Quotation Sales Create'],
        'store'   => ['store_quotation_sales', 'Quotation Sales Store'],
        'show'    => ['show_quotation_sales', 'Quotation Sales Show'],
        'edit'    => ['edit_quotation_sales', 'Quotation Sales Edit'],
        'update'  => ['update_quotation_sales', 'Quotation Sales Update'],
        'destroy' => ['delete_quotation_sales', 'Quotation Sales Delete'],
    ];

    public function pdf($id)
    {
        $quotation = Quotation::findOrFail($id);
        $customer = Customer::findOrFail($quotation->customer_id);

        $pdf = \PDF::loadView('quotation::print', [
            'quotation' => $quotation,
            'customer' => $customer,
        ])->setPaper('a4');

        return $pdf->stream('quotation-'. $quotation->reference .'.pdf');
    }

    public function index(Quotation $quotation) {
        abort_if(Gate::denies('create_quotation_sales'), 403);

        $quotation_details = $quotation->quotationDetails;

        Cart::instance('sale')->destroy();

        $cart = Cart::instance('sale');

        foreach ($quotation_details as $quotation_detail) {
            $cart->add([
                'id'      => $quotation_detail->product_id,
                'name'    => $quotation_detail->product_name,
                'qty'     => $quotation_detail->quantity,
                'price'   => $quotation_detail->price,
                'weight'  => 1,
                'options' => [
                    'product_discount' => $quotation_detail->product_discount_amount,
                    'product_discount_type' => $quotation_detail->product_discount_type,
                    'sub_total'   => $quotation_detail->sub_total,
                    'code'        => $quotation_detail->product_code,
                    'stock'       => Product::findOrFail($quotation_detail->product_id)->product_quantity,
                    'product_tax' => $quotation_detail->product_tax_amount,
                    'unit_price'  => $quotation_detail->unit_price
                ]
            ]);
        }

        return view('pos::quotation.quotation-sales.create', [
            'quotation_id' => $quotation->id,
            'sale' => $quotation
        ]);
    }
}
