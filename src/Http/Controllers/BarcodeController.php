<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class BarcodeController extends Controller
{
    public static $permissions = [
        'printBarcode'   => ['print_barcodes', 'Print Barcodes'],
    ];

    public function printBarcode() {
        abort_if(Gate::denies('print_barcodes'), 403);

        return view('pos::barcode.index');
    }

}
