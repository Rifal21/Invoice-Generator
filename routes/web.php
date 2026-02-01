<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProfitController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\VehicleRentalInvoiceController;
use App\Http\Controllers\RiceDeliveryController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\RiceOrderRecapController;
use App\Http\Controllers\RadioController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DediInvoiceController;
use App\Http\Controllers\KitchenIncentiveController;


// Auth Routes
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('invoices.index');
    });

    Route::post('invoices/scan', [ScanController::class, 'scan'])->name('invoices.scan');

    // Inventory Routes
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('inventory/{product}/adjust', [InventoryController::class, 'adjustStock'])->name('inventory.adjust');
    Route::get('inventory/{product}/history', [InventoryController::class, 'history'])->name('inventory.history');

    // POS Routes
    Route::get('pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    // Profit Report Routes
    Route::get('profit', [ProfitController::class, 'index'])->name('profit.index');
    Route::get('profit/export-all-pdf', [ProfitController::class, 'exportAllPdf'])->name('profit.export-all-pdf');
    Route::get('profit/{id}/export-pdf', [ProfitController::class, 'exportInvoicePdf'])->name('profit.export-invoice-pdf');

    Route::resource('customers', CustomerController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('categories', CategoryController::class);
    Route::patch('products/{product}/quick-update', [ProductController::class, 'quickUpdate'])->name('products.quick-update');
    Route::post('products/bulk-delete', [ProductController::class, 'bulkDestroy'])->name('products.bulk-delete');
    Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
    Route::delete('products/{product}/image', [ProductController::class, 'deleteImage'])->name('products.delete-image'); // New Route
    Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
    Route::resource('products', ProductController::class);

    // Custom Invoice Routes
    Route::get('invoices/{invoice}/export-pdf', [InvoiceController::class, 'exportPdf'])->name('invoices.export-pdf');
    Route::get('invoices/{invoice}/export-excel', [InvoiceController::class, 'exportExcel'])->name('invoices.export-excel');
    Route::post('invoices/bulk-export-pdf', [InvoiceController::class, 'bulkExportPdf'])->name('invoices.bulk-export-pdf');
    Route::get('invoices/bulk-export-pdf', [InvoiceController::class, 'bulkExportPdf']);
    Route::post('invoices/print-multi-pdf', [InvoiceController::class, 'printMultiPdf'])->name('invoices.print-multi-pdf');
    Route::post('invoices/send-telegram', [InvoiceController::class, 'sendToTelegram'])->name('invoices.send-telegram');
    Route::post('invoices/send-customer', [InvoiceController::class, 'sendToCustomer'])->name('invoices.send-customer');
    Route::post('invoices/send-whatsapp', [InvoiceController::class, 'sendToWhatsApp'])->name('invoices.send-whatsapp');
    Route::post('invoices/send-whapi', [InvoiceController::class, 'sendToWhapi'])->name('invoices.send-whapi');
    Route::post('invoices/bulk-delete', [InvoiceController::class, 'bulkDestroy'])->name('invoices.bulk-delete');

    Route::resource('invoices', InvoiceController::class);

    // Radio Routes
    Route::get('radio', [RadioController::class, 'index'])->name('radio.index');
    Route::get('radio/search', [RadioController::class, 'search'])->name('radio.search');
    Route::post('radio/request', [RadioController::class, 'requestSong'])->name('radio.request');
    Route::get('radio/status', [RadioController::class, 'getCurrentStatus'])->name('radio.status');
    Route::post('radio/skip', [RadioController::class, 'skipCurrent'])->name('radio.skip');

    // Global Chat Routes
    Route::get('chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('chat/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::get('chat/latest', [ChatController::class, 'latestMessage'])->name('chat.latest');
    Route::post('chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('chat/clear', [ChatController::class, 'clearChat'])->name('chat.clear');
    Route::get('chat/users', [ChatController::class, 'getUsers'])->name('chat.users');

    // Notification Routes
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    // Notification Routes
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Dedi Invoice Routes
    Route::get('dedi-invoices/{dedi_invoice}/export-pdf', [DediInvoiceController::class, 'exportPdf'])->name('dedi-invoices.export-pdf');
    Route::resource('dedi-invoices', DediInvoiceController::class);

    // Kitchen Incentive Routes
    Route::get('kitchen-incentives/{kitchenIncentive}/export-pdf', [KitchenIncentiveController::class, 'exportPdf'])->name('kitchen-incentives.export-pdf');
    Route::resource('kitchen-incentives', KitchenIncentiveController::class);

    // Activity Logs
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    // Vehicle Rental Invoice Routes
    Route::get('vehicle-rentals/next-number', [VehicleRentalInvoiceController::class, 'getNextNumber'])->name('vehicle-rentals.next-number');
    Route::get('vehicle-rentals/{vehicleRental}/export-pdf', [VehicleRentalInvoiceController::class, 'exportPdf'])->name('vehicle-rentals.export-pdf');
    Route::resource('vehicle-rentals', VehicleRentalInvoiceController::class);

    // Rice Delivery Routes
    Route::get('rice-deliveries/next-number', [RiceDeliveryController::class, 'getNextNumber'])->name('rice-deliveries.next-number');
    Route::get('rice-deliveries/{riceDelivery}/export-pdf', [RiceDeliveryController::class, 'exportPdf'])->name('rice-deliveries.export-pdf');
    Route::resource('rice-deliveries', RiceDeliveryController::class);

    // Delivery Order Routes (Surat Jalan)
    Route::get('delivery-orders/next-number', [DeliveryOrderController::class, 'getNextNumber'])->name('delivery-orders.next-number');
    Route::get('delivery-orders/{deliveryOrder}/export-pdf', [DeliveryOrderController::class, 'exportPdf'])->name('delivery-orders.export-pdf');
    Route::resource('delivery-orders', DeliveryOrderController::class);

    // Rice Order Recap Routes
    Route::get('rice-order-recap', [RiceOrderRecapController::class, 'index'])->name('rice-order-recap.index');
    Route::get('rice-order-recap/export-pdf', [RiceOrderRecapController::class, 'exportPdf'])->name('rice-order-recap.export-pdf');



    // Admin, Ketua & Admin Absensi
    Route::middleware(['role:super_admin,ketua,admin_absensi'])->group(function () {
        Route::get('attendance', [AttendanceController::class, 'publicScan'])->name('attendance.public');
        Route::post('attendance/scan', [AttendanceController::class, 'scan'])->name('attendance.scan');
        Route::post('attendance/check-status', [AttendanceController::class, 'checkStatus'])->name('attendance.check-status');

        // Attendance Admin Routes
        Route::get('attendance/settings', [AttendanceController::class, 'settings'])->name('attendance.settings');
        Route::post('attendance/settings', [AttendanceController::class, 'updateSettings'])->name('attendance.update-settings');
        Route::get('attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
        Route::patch('attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
        Route::delete('attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');
        Route::get('attendance/count', [AttendanceController::class, 'getAttendanceCount'])->name('attendance.count');
    });

    // Admin & Ketua only (Financials & Salaries)
    Route::middleware(['role:super_admin,ketua'])->group(function () {
        Route::resource('salaries', SalaryController::class);
        Route::post('salaries/{salary}/pay', [SalaryController::class, 'markAsPaid'])->name('salaries.mark-as-paid');
        Route::get('salaries/{salary}/print', [SalaryController::class, 'printSlip'])->name('salaries.print');

        // Financial Report Routes
        Route::get('finance/summary', [FinancialReportController::class, 'index'])->name('finance.summary');
        Route::get('finance/export-pdf', [FinancialReportController::class, 'exportPdf'])->name('finance.export-pdf');
        Route::resource('expenses', ExpenseController::class);
    });

    // Admin Only
    Route::middleware(['role:super_admin,ketua'])->group(function () {
        Route::get('qr-code/user/{code}', [UserController::class, 'generateQR'])->name('users.qr');
        Route::resource('users', UserController::class);
    });
});


Route::get('/test-wa', function () {
    $apiUrl = 'https://waapi.fkstudio.my.id';
    $instance = 'default';
    $phone = '6285179599150';
    $chatId = $phone . '@c.us';

    try {
        $response = Http::withHeaders([
            'X-Api-Key' => '80b4a1a50d074b669b9cc67251059004', // API key WAHA CORE
            'Content-Type' => 'application/json',
        ])->post("{$apiUrl}/api/sendText", [
            'session' => $instance,
            'chatId' => $chatId,
            'text' => "🔔 Tes Koneksi WAHA CORE Sukses!\nJam: " . now()->format('H:i:s'),
        ]);

        return response()->json([
            'status' => $response->status(),
            'body' => $response->json(),
            'url_used' => "{$apiUrl}/api/sendText"
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'url_target' => $apiUrl
        ], 500);
    }
});
