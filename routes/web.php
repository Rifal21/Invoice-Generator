<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    return redirect()->route('invoices.index');
});

Route::resource('categories', CategoryController::class);
Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
Route::resource('products', ProductController::class);

// Custom Invoice Routes (Must be above resource to avoid conflict with {invoice} parameter)
Route::get('invoices/{invoice}/export-pdf', [InvoiceController::class, 'exportPdf'])->name('invoices.export-pdf');
Route::get('invoices/{invoice}/export-excel', [InvoiceController::class, 'exportExcel'])->name('invoices.export-excel');
Route::post('invoices/bulk-export-pdf', [InvoiceController::class, 'bulkExportPdf'])->name('invoices.bulk-export-pdf');
Route::get('invoices/bulk-export-pdf', [InvoiceController::class, 'bulkExportPdf']);

Route::resource('invoices', InvoiceController::class);
