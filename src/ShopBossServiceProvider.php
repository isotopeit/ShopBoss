<?php

namespace Isotope\ShopBoss;

use Livewire\Livewire;
use Isotope\ShopBoss\Models\Product;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Isotope\ShopBoss\Http\Livewire\ShopBoss\Filter;
use Isotope\ShopBoss\Http\Livewire\ProductCart;
use Isotope\ShopBoss\Http\Livewire\ShopBoss\Checkout;
use Isotope\ShopBoss\Http\Livewire\SearchProduct;
use Isotope\ShopBoss\Http\Livewire\ShopBoss\ProductList;
use Isotope\ShopBoss\Http\Livewire\Reports\SalesReport;
use Isotope\ShopBoss\Http\Livewire\Reports\PaymentsReport;
use Isotope\ShopBoss\Http\Livewire\Adjustment\ProductTable;
use Isotope\ShopBoss\Http\Livewire\Reports\PurchasesReport;
use Isotope\ShopBoss\Http\Middleware\HandleInertiaRequests;
use Isotope\ShopBoss\Http\Livewire\Reports\ProfitLossReport;
use Isotope\ShopBoss\Http\Livewire\Reports\SalesReturnReport;
use Isotope\ShopBoss\Http\Livewire\Reports\PurchasesReturnReport;
use Isotope\ShopBoss\Http\Livewire\Barcode\ProductTable as BarcodeProductTable;

class ShopBossServiceProvider extends ServiceProvider
{
    public function boot()
    {
        foreach (glob(__DIR__ . '/Http/Helpers/*.php') as $filename) {
            require_once $filename;
        }

        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'shopboss');

        $this->publishes([
            __DIR__ . '/../stubs/assets' => public_path('isotope/shopboss'),
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/ipermissions.php', 'ipermissions');
        $this->mergeConfigFrom(__DIR__ . '/Config/sidebar.php', 'sidebar');
    }
}
