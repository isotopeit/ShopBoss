<?php

use Illuminate\Support\Facades\Route;
use Isotope\ShopBoss\Http\Controllers\PosController;
use Isotope\ShopBoss\Http\Controllers\HomeController;
use Isotope\ShopBoss\Http\Controllers\SaleController;
use Isotope\ShopBoss\Http\Controllers\StockController;
use Isotope\Metronic\Http\Middlewares\LocaleMiddleware;
use Isotope\ShopBoss\Http\Controllers\BranchController;
use Isotope\ShopBoss\Http\Controllers\UploadController;
use Isotope\ShopBoss\Http\Controllers\BarcodeController;
use Isotope\ShopBoss\Http\Controllers\ExpenseController;
use Isotope\ShopBoss\Http\Controllers\ProductController;
use Isotope\ShopBoss\Http\Controllers\ProfileController;
use Isotope\ShopBoss\Http\Controllers\ReportsController;
use Isotope\ShopBoss\Http\Controllers\CurrencyController;
use Isotope\ShopBoss\Http\Controllers\PurchaseController;
use Isotope\ShopBoss\Http\Controllers\CustomersController;
use Isotope\ShopBoss\Http\Controllers\QuotationController;
use Isotope\ShopBoss\Http\Controllers\SuppliersController;
use Isotope\ShopBoss\Http\Controllers\AdjustmentController;
use Isotope\ShopBoss\Http\Controllers\BranchUserController;
use Isotope\ShopBoss\Http\Controllers\CategoriesController;
use Isotope\ShopBoss\Http\Controllers\SalesReturnController;
use Isotope\ShopBoss\Http\Controllers\SalePaymentsController;
use Isotope\ShopBoss\Http\Controllers\QuotationSalesController;
use Isotope\ShopBoss\Http\Controllers\PurchasesReturnController;
use Isotope\ShopBoss\Http\Controllers\PurchasePaymentsController;
use Isotope\ShopBoss\Http\Controllers\ExpenseCategoriesController;
use Isotope\ShopBoss\Http\Controllers\SaleReturnPaymentsController;
use Isotope\ShopBoss\Http\Controllers\SendQuotationEmailController;
use Isotope\ShopBoss\Http\Controllers\PurchaseReturnPaymentsController;

Route::group(['middleware' => ['web', 'auth','authorization', LocaleMiddleware::class]], function () {
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::post('/app/pos', [PosController::class, 'store'])->name('app.pos.store');
    Route::get('/app/pos', [PosController::class, 'index'])->name('app.pos.index');

    Route::get('/sales-purchases/chart-data', [HomeController::class, 'salesPurchasesChart'])->name('sales-purchases.chart');
    Route::get('/current-month/chart-data', [HomeController::class, 'currentMonthChart'])->name('current-month.chart');
    Route::get('/payment-flow/chart-data', [HomeController::class, 'paymentChart'])->name('payment-flow.chart');

    Route::resource('adjustments', AdjustmentController::class);
    Route::resource('shopboss-branches', BranchController::class)->except('show');
    Route::post('/branch-enable', [BranchController::class, 'branchEnable'])->name('shopboss.branchEnable');
    Route::resource('shop-branch-user', BranchUserController::class);
    Route::resource('currencies', CurrencyController::class)->except('show');
    Route::resource('expense-categories', ExpenseCategoriesController::class)->except('show', 'create');
    Route::resource('expenses', ExpenseController::class)->except('show');
    Route::resource('customers', CustomersController::class);
    Route::resource('suppliers', SuppliersController::class);

    Route::get('/customer-api', [CustomersController::class, 'customerSelecr2']);
    Route::get('/supplier-api', [SuppliersController::class, 'supplierSelecr2']);

    Route::get('/products/print-barcode', [BarcodeController::class, 'printBarcode'])->name('barcode.print');
    Route::resource('products', ProductController::class);
    Route::resource('product-categories', CategoriesController::class)->except('create', 'show');

    Route::get('/purchases/pdf/{id}', [PurchaseController::class, 'pdf'])->name('purchases.pdf');
    Route::resource('purchases', PurchaseController::class);
    Route::resource('purchase-payments', PurchasePaymentsController::class);
    Route::resource('purchase-returns', PurchasesReturnController::class);
    Route::get('/purchase-returns/pdf/{id}', [PurchasesReturnController::class, 'pdf'])->name('purchase-returns.pdf');
    Route::resource('purchase-return-payments', PurchaseReturnPaymentsController::class);

    Route::get('/quotations/pdf/{id}', [QuotationSalesController::class, 'pdf'])->name('quotations.pdf');
    Route::get('/quotation/mail/{quotation}', [SendQuotationEmailController::class, 'index'])->name('quotation.email');
    Route::get('/quotation-sales/{quotation}', [QuotationSalesController::class, 'index'])->name('quotation-sales.create');
    Route::resource('quotations', QuotationController::class);

    Route::get('/profit-loss-report', [ReportsController::class, 'profitLossReport'])->name('profit-loss-report.index');
    Route::get('/payments-report', [ReportsController::class, 'paymentsReport'])->name('payments-report.index');
    Route::get('/sales-report', [ReportsController::class, 'salesReport'])->name('sales-report.index');
    Route::get('/purchases-report', [ReportsController::class, 'purchasesReport'])->name('purchases-report.index');
    Route::get('/sales-return-report', [ReportsController::class, 'salesReturnReport'])->name('sales-return-report.index');
    Route::get('/purchases-return-report', [ReportsController::class, 'purchasesReturnReport'])->name('purchases-return-report.index');
    Route::get('/product-wise-sele-report', [ReportsController::class, 'productWiseSeleReport'])->name('product_wise_sele_report');
    Route::get('/product-wise-purchase-report', [ReportsController::class, 'productWisePurchaseReport'])->name('product_wise_purchase_report');
    Route::get('/product-wise-sele-return-report', [ReportsController::class, 'productWiseSeleReturnReport'])->name('product_wise_sele_return_report');
    Route::get('/product-wise-purchase-return-report', [ReportsController::class, 'productWisePurchaseReturnReport'])->name('product_wise_purchase_return_report');

    Route::get('/sales/pdf/{id}', [SaleController::class, 'pdf'])->name('sales.pdf');
    Route::get('/sales/pos/pdf/{id}', [SaleController::class, 'posPdf'])->name('sales.pos.pdf');

    Route::resource('sales', SaleController::class);
    Route::resource('sale-payments', SalePaymentsController::class);

    Route::get('/sale-returns/pdf/{id}', [SalesReturnController::class, 'pdf'])->name('sale-returns.pdf');

    Route::resource('sale-returns', SalesReturnController::class);

    Route::get('/stock', [StockController::class, 'stock'])->name('stock');

    Route::get('/sale-return-payments/{sale_return_id}', [SaleReturnPaymentsController::class, 'index'])->name('sale-return-payments.index');
    Route::get('/sale-return-payments/{sale_return_id}/create', [SaleReturnPaymentsController::class, 'create'])->name('sale-return-payments.create');
    Route::post('/sale-return-payments/store', [SaleReturnPaymentsController::class, 'store'])->name('sale-return-payments.store');
    Route::get('/sale-return-payments/{sale_return_id}/edit/{saleReturnPayment}', [SaleReturnPaymentsController::class, 'edit'])->name('sale-return-payments.edit');
    Route::patch('/sale-return-payments/update/{saleReturnPayment}', [SaleReturnPaymentsController::class, 'update'])->name('sale-return-payments.update');
    Route::delete('/sale-return-payments/destroy/{saleReturnPayment}', [SaleReturnPaymentsController::class, 'destroy'])->name('sale-return-payments.destroy');

    // Route::patch('/settings/smtp', [SettingController::class, 'updateSmtp'])->name('settings.smtp.update');
    // Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    // Route::patch('/settings', [SettingController::class, 'update'])->name('settings.update');

    Route::post('/dropzone/upload', [UploadController::class, 'dropzoneUpload'])->name('dropzone.upload');
    Route::post('/dropzone/delete', [UploadController::class, 'dropzoneDelete'])->name('dropzone.delete');
    Route::post('/filepond/upload', [UploadController::class, 'filepondUpload'])->name('filepond.upload');
    Route::delete('/filepond/delete', [UploadController::class, 'filepondDelete'])->name('filepond.delete');

    Route::get('/user/profile', [ProfileController::class, 'edit'])->name('shop-profile.edit');
    Route::patch('/user/profile', [ProfileController::class, 'update'])->name('shop-profile.update');
    Route::patch('/user/password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');
    // Route::resource('users', UsersController::class)->except('show');
    // Route::resource('roles', RolesController::class)->except('show');
});
