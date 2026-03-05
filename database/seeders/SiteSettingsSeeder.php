<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // General Settings
        $settings = [
            ['key' => 'app_name', 'value' => 'KOPERASI JR', 'type' => 'text', 'group' => 'general'],
            ['key' => 'brand_name', 'value' => 'KOPERASI JR', 'type' => 'text', 'group' => 'general'],
            ['key' => 'brand_logo', 'value' => 'images/kopinvoice.png', 'type' => 'image', 'group' => 'general'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        // Sidebar Items
        $sidebar = [
            // Top Level
            ['label' => 'Dashboard', 'icon' => 'fas fa-home text-indigo-400', 'route' => 'dashboard', 'order' => 1],
            ['label' => 'Barcode Saya', 'icon' => 'fas fa-qrcode text-emerald-400', 'route' => 'users.my-barcode', 'order' => 2],
            ['label' => 'AI Insights Analyst', 'icon' => 'fas fa-brain text-pink-400', 'route' => 'ai.index', 'order' => 3],
            ['label' => 'Dokumen Legalitas', 'icon' => 'fas fa-folder-open text-yellow-500', 'route' => 'documents.index', 'order' => 4],
            ['label' => 'Laporan Pemeriksaan', 'icon' => 'fas fa-clipboard-check text-cyan-400', 'route' => 'examination-reports.index', 'order' => 5],
            
            // Master Data Dropdown
            ['label' => 'Master Data', 'icon' => 'fas fa-database', 'route' => null, 'order' => 6, 'children' => [
                ['label' => 'Kategori Produk', 'icon' => 'fas fa-tags', 'route' => 'categories.index'],
                ['label' => 'Pelanggan', 'icon' => 'fas fa-users', 'route' => 'customers.index'],
                ['label' => 'Supplier', 'icon' => 'fas fa-truck', 'route' => 'suppliers.index'],
                ['label' => 'Nota Supplier', 'icon' => 'fas fa-file-invoice-dollar', 'route' => 'supplier-notas.index'],
                ['label' => 'Produk', 'icon' => 'fas fa-boxes', 'route' => 'products.index'],
            ]],
            
            // Operasional Dropdown
            ['label' => 'Operasional', 'icon' => 'fas fa-cash-register', 'route' => null, 'order' => 7, 'children' => [
                ['label' => 'Nota Pengiriman Beras', 'icon' => 'fas fa-shipping-fast', 'route' => 'rice-deliveries.index'],
                ['label' => 'Surat Jalan', 'icon' => 'fas fa-truck', 'route' => 'delivery-orders.index'],
                ['label' => 'Invoice Generator', 'icon' => 'fas fa-file-invoice', 'route' => 'invoices.index'],
                ['label' => 'Kalender Invoice', 'icon' => 'fas fa-calendar-alt text-amber-400', 'route' => 'invoices.calendar'],
                ['label' => 'Invoice Sewa Kendaraan', 'icon' => 'fas fa-car', 'route' => 'vehicle-rentals.index'],
                ['label' => 'Nota Faktur H Dedi', 'icon' => 'fas fa-file-invoice', 'route' => 'dedi-invoices.index'],
                ['label' => 'Invoice Insentif Dapur', 'icon' => 'fas fa-utensils', 'route' => 'kitchen-incentives.index'],
                ['label' => 'Buat Pesanan (POS)', 'icon' => 'fas fa-shopping-cart', 'route' => 'pos.index'],
                ['label' => 'Global Chat', 'icon' => 'fas fa-comments text-emerald-400', 'route' => 'chat.index'],
                ['label' => 'Live Radio', 'icon' => 'fas fa-radio text-indigo-400', 'route' => 'radio.index'],
            ]],
            
            // Inventori Dropdown
            ['label' => 'Gudang & Stok', 'icon' => 'fas fa-warehouse', 'route' => null, 'order' => 8, 'children' => [
                ['label' => 'Gudang Utama', 'icon' => 'fas fa-cubes', 'route' => 'inventory.index'],
            ]],
            
            // Analisa Keuangan Dropdown
            ['label' => 'Analisa Keuangan', 'icon' => 'fas fa-chart-pie', 'route' => null, 'order' => 9, 'children' => [
                ['label' => 'Ringkasan Keuangan', 'icon' => 'fas fa-chart-line', 'route' => 'finance.summary'],
                ['label' => 'Pengeluaran', 'icon' => 'fas fa-money-bill-wave', 'route' => 'expenses.index'],
                ['label' => 'Detail Laba Rugi', 'icon' => 'fas fa-sack-dollar', 'route' => 'profit.index'],
                ['label' => 'Rekap Pesanan Beras', 'icon' => 'fas fa-list-check', 'route' => 'rice-order-recap.index'],
            ]],
            
            // Kepegawaian Dropdown
            ['label' => 'Kepegawaian', 'icon' => 'fas fa-user-tie', 'route' => null, 'order' => 10, 'children' => [
                ['label' => 'Pegawai', 'icon' => 'fas fa-users', 'route' => 'users.index'],
                ['label' => 'Gaji Pegawai', 'icon' => 'fas fa-wallet', 'route' => 'salaries.index'],
                ['label' => 'Laporan Absensi', 'icon' => 'fas fa-clipboard-list', 'route' => 'attendance.report'],
                ['label' => 'Input Absensi Bulk', 'icon' => 'fas fa-users-cog', 'route' => 'attendance.bulk'],
                ['label' => 'Pengaturan Absensi', 'icon' => 'fas fa-cog', 'route' => 'attendance.settings'],
            ]],
            
            // Misc
            ['label' => 'Log Aktivitas', 'icon' => 'fas fa-history text-amber-500', 'route' => 'activity-logs.index', 'order' => 11],
            ['label' => 'Monitor Login', 'icon' => 'fas fa-desktop text-green-500', 'route' => 'monitor.index', 'order' => 12],
            ['label' => 'Cloud Backup', 'icon' => 'fab fa-google-drive text-blue-500', 'route' => 'backup.index', 'order' => 13],
            ['label' => 'Settings', 'icon' => 'fas fa-cog text-gray-400', 'route' => 'settings.index', 'order' => 14],
        ];

        foreach ($sidebar as $item) {
            $parent = \App\Models\SidebarItem::updateOrCreate(
                ['label' => $item['label']],
                [
                    'icon' => $item['icon'],
                    'route' => $item['route'],
                    'order' => $item['order'],
                    'is_active' => true,
                ]
            );

            if (isset($item['children'])) {
                foreach ($item['children'] as $childOrder => $child) {
                    \App\Models\SidebarItem::updateOrCreate(
                        ['label' => $child['label'], 'parent_id' => $parent->id],
                        [
                            'icon' => $child['icon'],
                            'route' => $child['route'],
                            'order' => $childOrder + 1,
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}
