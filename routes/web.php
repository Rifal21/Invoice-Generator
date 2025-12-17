<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    return redirect()->route('invoices.index');
});

Route::resource('categories', CategoryController::class);
Route::resource('products', ProductController::class);
Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
Route::resource('invoices', InvoiceController::class);
Route::get('invoices/{invoice}/export-pdf', [InvoiceController::class, 'exportPdf'])->name('invoices.export-pdf');
Route::get('invoices/{invoice}/export-excel', [InvoiceController::class, 'exportExcel'])->name('invoices.export-excel');
