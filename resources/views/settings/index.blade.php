@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
    <div class="max-w-7xl mx-auto" x-data="{
        tab: 'general',
        brand_name: '{{ $settings['brand_name'] ?? 'KOPERASI KONSUMEN JEMBAR RAHAYU SEJAHTERA' }}',
        company_address: '{{ $settings['company_address'] ?? 'JL. Moch. Bagowi Kp. Bojong RT003 / RW002' }}',
        company_phone: '{{ $settings['company_phone'] ?? '+6281546527513' }}',
        company_email: '{{ $settings['company_email'] ?? 'koperasikonsumenjembarrahayu@gmail.com' }}',
        bank_info: '{{ $settings['bank_info'] ?? '8155688615 BNI a/n KOPERASI JEMBAR RAHAYU SEJAHTERA' }}',
        signature_name: '{{ $settings['signature_name'] ?? 'Rizki Ichsan Al-Fath' }}',
        signature_title: '{{ $settings['signature_title'] ?? 'Ketua Pengurus' }}',
        primary_color: '{{ $settings['primary_color'] ?? '#203764' }}',
        logo_preview: '{{ isset($settings['brand_logo']) ? (Storage::disk('public')->exists($settings['brand_logo']) ? asset('storage/' . $settings['brand_logo']) : asset($settings['brand_logo'])) : asset('images/kopinvoice.png') }}',
        sig_preview: '{{ isset($settings['signature_image']) ? (Storage::disk('public')->exists($settings['signature_image']) ? asset('storage/' . $settings['signature_image']) : asset($settings['signature_image'])) : asset('images/ttd.png') }}',
    
        handleLogoUpload(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (f) => { this.logo_preview = f.target.result; };
                reader.readAsDataURL(file);
            }
        },
        handleSigUpload(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (f) => { this.sig_preview = f.target.result; };
                reader.readAsDataURL(file);
            }
        }
    }">
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">PENGATURAN</h1>
                <p class="text-gray-500 font-medium">Kelola identitas aplikasi dan menu navigasi</p>
            </div>

            <div class="flex bg-white p-1 rounded-xl shadow-sm border border-gray-200 self-start">
                <button @click="tab = 'general'"
                    :class="tab === 'general' ? 'bg-indigo-600 text-white shadow-md' :
                        'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-all duration-200">
                    <i class="fas fa-sliders mr-2"></i> Umum
                </button>
                <button @click="tab = 'sidebar'"
                    :class="tab === 'sidebar' ? 'bg-indigo-600 text-white shadow-md' :
                        'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-all duration-200">
                    <i class="fas fa-list-ul mr-2"></i> Sidebar
                </button>
                <button @click="tab = 'template'"
                    :class="tab === 'template' ? 'bg-indigo-600 text-white shadow-md' :
                        'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-all duration-200">
                    <i class="fas fa-file-invoice mr-2"></i> Template Print
                </button>
            </div>
        </div>

        <!-- General Settings -->
        <div x-show="tab === 'general'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1">
                    <h3 class="text-lg font-bold text-gray-900">Identitas Brand</h3>
                    <p class="text-sm text-gray-500 mt-1">Sesuaikan nama dan logo yang muncul di aplikasi.</p>
                </div>

                <div class="lg:col-span-2">
                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data"
                        class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        @csrf
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">Nama
                                        Aplikasi</label>
                                    <input type="text" name="app_name" value="{{ $settings['app_name'] ?? '' }}"
                                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all outline-none font-medium">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">Nama
                                        Brand</label>
                                    <input type="text" name="brand_name" x-model="brand_name"
                                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all outline-none font-medium">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">Logo
                                    Brand</label>
                                <div class="flex items-center gap-6">
                                    <div
                                        class="h-24 w-24 bg-gray-100 rounded-2xl flex items-center justify-center border-2 border-dashed border-gray-200 overflow-hidden group relative">
                                        <img :src="logo_preview" class="h-full w-full object-contain p-2">
                                    </div>
                                    <div class="flex-1">
                                        <input type="file" name="brand_logo" @change="handleLogoUpload"
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all cursor-pointer">
                                        <p class="text-xs text-gray-400 mt-2 font-medium">Rekomendasi ukuran square (1:1),
                                            format PNG/SVG, Maks 2MB.</p>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="company_address" :value="company_address">
                            <input type="hidden" name="company_phone" :value="company_phone">
                            <input type="hidden" name="company_email" :value="company_email">
                            <input type="hidden" name="bank_info" :value="bank_info">
                            <input type="hidden" name="signature_name" :value="signature_name">
                            <input type="hidden" name="signature_title" :value="signature_title">
                            <input type="hidden" name="primary_color" :value="primary_color">
                        </div>
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-black px-6 py-2.5 rounded-xl shadow-lg shadow-indigo-600/20 transition-all flex items-center gap-2">
                                <i class="fas fa-save"></i> SIMPAN PERUBAHAN
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Template Settings -->
        <div x-show="tab === 'template'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="app_name" value="{{ $settings['app_name'] ?? '' }}">
                <input type="hidden" name="brand_name" :value="brand_name">

                <input type="hidden" name="company_address" :value="company_address">
                <input type="hidden" name="company_phone" :value="company_phone">
                <input type="hidden" name="company_email" :value="company_email">
                <input type="hidden" name="bank_info" :value="bank_info">
                <input type="hidden" name="signature_name" :value="signature_name">
                <input type="hidden" name="signature_title" :value="signature_title">
                <input type="hidden" name="primary_color" :value="primary_color">

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                            <h4 class="text-sm font-black text-gray-900 uppercase tracking-widest border-b pb-2">Informasi
                                Perusahaan</h4>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Alamat
                                        Lengkap</label>
                                    <textarea name="company_address" rows="3" x-model="company_address"
                                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Telepon</label>
                                        <input type="text" name="company_phone" x-model="company_phone"
                                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Email</label>
                                        <input type="email" name="company_email" x-model="company_email"
                                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                            <h4 class="text-sm font-black text-gray-900 uppercase tracking-widest border-b pb-2">Pembayaran
                            </h4>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Informasi Rekening Bank
                                    (Tampil di Invoice)</label>
                                <input type="text" name="bank_info" x-model="bank_info"
                                    placeholder="Contoh: 8155688615 BNI a/n KOPERASI JEMBAR RAHAYU"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                            <h4 class="text-sm font-black text-gray-900 uppercase tracking-widest border-b pb-2">Tema &
                                Warna
                            </h4>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Warna Utama (Primary
                                    Color)</label>
                                <div class="flex items-center gap-4">
                                    <input type="color" name="primary_color" x-model="primary_color"
                                        class="h-12 w-20 bg-transparent cursor-pointer rounded-lg overflow-hidden border-0">
                                    <input type="text" x-model="primary_color"
                                        class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500 outline-none uppercase">
                                </div>
                                <p class="text-[10px] text-gray-400 mt-2">Warna ini akan digunakan pada Header PDF, tabel,
                                    dan label total.</p>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                            <h4 class="text-sm font-black text-gray-900 uppercase tracking-widest border-b pb-2">Tanda
                                Tangan (Signature)</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Nama Penanda
                                        Tangan</label>
                                    <input type="text" name="signature_name" x-model="signature_name"
                                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Jabatan</label>
                                    <input type="text" name="signature_title" x-model="signature_title"
                                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Gambar Tanda Tangan
                                    (PNG Transparan)</label>
                                <div class="flex items-center gap-4">
                                    <div
                                        class="h-20 w-32 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200 overflow-hidden">
                                        <img :src="sig_preview" class="h-full object-contain p-1">
                                    </div>
                                    <input type="file" name="signature_image" @change="handleSigUpload"
                                        class="text-xs file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-black px-8 py-3 rounded-xl shadow-lg shadow-indigo-600/20 transition-all">
                                SIMPAN TEMPLATE
                            </button>
                        </div>
                    </div>

                    <!-- PREVIEW PANEL -->
                    <div class="lg:col-span-3">
                        <div class="sticky top-24">
                            <h3
                                class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="fas fa-eye"></i> LIVE PREVIEW (INVOICE)
                            </h3>
                            <div class="bg-slate-200 shadow-2xl rounded-xl border-4 border-slate-300 overflow-hidden relative p-12"
                                style="min-height: 800px; aspect-ratio: 1/1.414; zoom: 0.6;">
                                <!-- PDF Mockup - Paper with solid border -->
                                <div
                                    class="bg-white border-[3px] border-black text-black font-sans min-h-full flex flex-col shadow-2xl">
                                    <div class="flex-1">
                                        <!-- Header -->
                                        <div :style="`background-color: ${primary_color}`" class="text-white p-8 mb-6">
                                            <div class="flex justify-between items-start mb-2">
                                                <!-- Logo -->
                                                <div
                                                    class="w-24 h-24 bg-white rounded-md p-1 flex items-center justify-center overflow-hidden">
                                                    <img :src="logo_preview"
                                                        class="max-w-full max-h-full object-contain">
                                                </div>

                                                <!-- Company Info Centered -->
                                                <div class="text-center flex-1 px-4">
                                                    <div class="text-[20px] font-bold uppercase leading-tight tracking-tight mb-2"
                                                        x-text="brand_name"></div>
                                                    <div class="text-[12px] leading-relaxed">
                                                        <div x-text="company_address"></div>
                                                        <div>Telepon : <span x-text="company_phone"></span></div>
                                                        <div>Email : <span x-text="company_email"></span></div>
                                                    </div>
                                                </div>

                                                <!-- Invoice Title & Meta -->
                                                <div class="text-right w-64">
                                                    <h1 class="text-[40px] font-bold leading-none mb-4">INVOICE</h1>
                                                    <div class="text-[14px]">
                                                        <div class="flex justify-end gap-2 mb-1">
                                                            <span>No :</span>
                                                            <span
                                                                class="border-b border-white min-w-[150px] text-center italic">INV-20260301-365-OPR</span>
                                                        </div>
                                                        <div class="flex justify-end gap-2">
                                                            <span>Tanggal :</span>
                                                            <span
                                                                class="border-b border-white min-w-[150px] text-center">01/03/2026</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Content -->
                                        <div class="px-10">
                                            <!-- Info Boxes -->
                                            <div class="flex justify-between items-start mb-10">
                                                <div class="border border-black p-0 w-72">
                                                    <div
                                                        class="text-[11px] text-gray-500 border-b border-black px-2 py-1 bg-gray-50">
                                                        Kepada Yth.</div>
                                                    <div class="font-bold text-lg px-2 py-3">SPPG DAPUR SEHAT KITA</div>
                                                </div>
                                                <div
                                                    class="border-2 border-black text-center w-72 overflow-hidden rounded-sm">
                                                    <div class="border-b border-black p-2 font-bold text-sm bg-gray-50">
                                                        Jumlah Yang Harus Di Bayar</div>
                                                    <div class="p-4 text-3xl font-bold italic tracking-tight">Rp989.500
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Main Table -->
                                            <table class="w-full border-collapse border border-black mb-8 text-[13px]">
                                                <thead :style="`background-color: ${primary_color}`" class="text-white">
                                                    <tr>
                                                        <th class="border border-black p-2 font-normal w-12">No.</th>
                                                        <th class="border border-black p-2 font-normal text-center">
                                                            Deskripsi</th>
                                                        <th class="border border-black p-2 font-normal w-20">Jumlah</th>
                                                        <th class="border border-black p-2 font-normal w-20">Volume</th>
                                                        <th class="border border-black p-2 font-normal w-32">Harga Satuan
                                                        </th>
                                                        <th class="border border-black p-2 font-normal w-32">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="border border-black p-2 text-center">1</td>
                                                        <td class="border border-black p-2">Mika 4c</td>
                                                        <td class="border border-black p-2 text-center">12</td>
                                                        <td class="border border-black p-2 text-center">Pack</td>
                                                        <td class="border border-black p-2 text-left">Rp 18.000</td>
                                                        <td class="border border-black p-2 text-right">Rp 216.000</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="border border-black p-2 text-center font-bold">2</td>
                                                        <td class="border border-black p-2 font-bold uppercase">PELAYANAN
                                                            CONTOH ITEM</td>
                                                        <td class="border border-black p-2 text-center font-bold">1</td>
                                                        <td class="border border-black p-2 text-center">Unit</td>
                                                        <td class="border border-black p-2 text-left">Rp 773.500</td>
                                                        <td class="border border-black p-2 text-right">Rp 773.500</td>
                                                    </tr>
                                                    @for ($i = 0; $i < 6; $i++)
                                                        <tr>
                                                            <td
                                                                class="border border-black p-2 text-center text-transparent">
                                                                .</td>
                                                            <td class="border border-black p-2"></td>
                                                            <td class="border border-black p-2"></td>
                                                            <td class="border border-black p-2"></td>
                                                            <td class="border border-black p-2"></td>
                                                            <td class="border border-black p-2"></td>
                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>

                                            <div class="flex justify-between items-start">
                                                <!-- Bank & Message -->
                                                <div class="w-3/5 text-center mt-6">
                                                    <div class="text-[13px] mb-1">Transfer ke No. Rek :</div>
                                                    <div class="font-bold text-[14px] uppercase" x-text="bank_info"></div>
                                                    <div class="mt-8 font-bold text-2xl italic tracking-wide">Terima Kasih
                                                    </div>
                                                </div>

                                                <!-- Summary Table -->
                                                <div class="w-[35%]">
                                                    <table class="w-full border-collapse border border-black">
                                                        <tr class="text-[13px]">
                                                            <td :style="`background-color: ${primary_color}`"
                                                                class="text-white p-2 border border-black w-24">Sub total
                                                            </td>
                                                            <td class="p-2 border border-black text-right font-medium">
                                                                Rp989.500</td>
                                                        </tr>
                                                        <tr class="text-[13px]">
                                                            <td :style="`background-color: ${primary_color}`"
                                                                class="text-white p-2 border border-black">Diskon</td>
                                                            <td class="p-2 border border-black text-right">-</td>
                                                        </tr>
                                                        <tr class="text-[13px]">
                                                            <td :style="`background-color: ${primary_color}`"
                                                                class="text-white p-2 border border-black">Pajak</td>
                                                            <td class="p-2 border border-black text-right">-</td>
                                                        </tr>
                                                        <tr class="text-[14px]">
                                                            <td :style="`background-color: ${primary_color}`"
                                                                class="text-white p-2 border border-black font-bold">Total
                                                            </td>
                                                            <td :style="`background-color: ${primary_color}`"
                                                                class="text-white p-2 border border-black text-right font-bold text-lg">
                                                                Rp989.500</td>
                                                        </tr>
                                                    </table>

                                                    <!-- Signature Section -->
                                                    <div class="mt-12 text-center">
                                                        <div class="text-[13px] mb-2">Hormat Kami,</div>
                                                        <div class="h-24 flex items-center justify-center my-2">
                                                            <img :src="sig_preview" class="max-h-full object-contain">
                                                        </div>
                                                        <div class="mt-2 text-center">
                                                            <div class="font-bold text-[16px] border-b border-black inline-block px-1 mb-1"
                                                                x-text="signature_name"></div>
                                                            <div class="text-[12px] font-medium" x-text="signature_title">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer Bar -->
                                    <div :style="`background-color: ${primary_color}`" class="h-8 w-full mt-auto"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar Settings -->
        <div x-show="tab === 'sidebar'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <div class="lg:col-span-1">
                    <h3 class="text-lg font-bold text-gray-900">Struktur Menu</h3>
                    <p class="text-sm text-gray-500 mt-1">Geser untuk mengubah urutan, klik toggle untuk
                        sembunyikan/tampilkan menu.</p>

                    <div class="mt-6 p-4 bg-indigo-50 rounded-2xl border border-indigo-100">
                        <h4 class="text-xs font-black text-indigo-700 uppercase tracking-widest mb-3">Tambah Menu Baru</h4>
                        <form action="{{ route('settings.sidebar.add') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="text-[10px] font-bold text-indigo-600 uppercase">Label</label>
                                <input type="text" name="label" required placeholder="Contoh: Laporan Baru"
                                    class="w-full bg-white border border-indigo-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-indigo-600 uppercase">Icon (FontAwesome)</label>
                                <input type="text" name="icon" placeholder="fas fa-star"
                                    class="w-full bg-white border border-indigo-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-indigo-600 uppercase">Route</label>
                                <input type="text" name="route" placeholder="invoices.index"
                                    class="w-full bg-white border border-indigo-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-indigo-600 uppercase">Grup (Parent)</label>
                                <select name="parent_id"
                                    class="w-full bg-white border border-indigo-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                                    <option value="">Tanpa Grup (Top Level)</option>
                                    @foreach ($sidebarItems->where('route', null) as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit"
                                class="w-full bg-indigo-600 text-white font-bold py-2 rounded-lg text-sm shadow-md hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-plus mr-1"></i> Tambah Menu
                            </button>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div id="sidebar-items-list" class="divide-y divide-gray-100">
                            @foreach ($sidebarItems as $item)
                                <div class="sidebar-row group" data-id="{{ $item->id }}">
                                    <div class="p-4 flex items-center gap-4 hover:bg-gray-50 transition-colors">
                                        <div class="cursor-move text-gray-300 hover:text-gray-500 drag-handle">
                                            <i class="fas fa-grip-vertical"></i>
                                        </div>
                                        <div
                                            class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 group-hover:bg-white group-hover:shadow-sm transition-all">
                                            <i class="{{ $item->icon }}"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-sm font-bold text-gray-900">{{ $item->label }}</h4>
                                            <p class="text-xs text-gray-400 font-medium">
                                                {{ $item->route ?? 'Dropdown Group' }}</p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <button onclick="toggleVisibility({{ $item->id }})"
                                                id="toggle-{{ $item->id }}"
                                                class="px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all {{ $item->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                                {{ $item->is_active ? 'TAMPIL' : 'TERSEMBUNYI' }}
                                            </button>
                                            <form action="{{ route('settings.sidebar.delete', $item) }}" method="POST"
                                                onsubmit="return confirm('Hapus menu ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                                                    <i class="fas fa-trash-can"></i>
                                                </button>
                                            </form>
                                            @if ($item->children->count() > 0)
                                                <button class="p-2 text-gray-400 hover:text-indigo-500 transition-colors"
                                                    onclick="toggleChildren({{ $item->id }})">
                                                    <i class="fas fa-chevron-down" id="chevron-{{ $item->id }}"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Children -->
                                    @if ($item->children->count() > 0)
                                        <div id="children-{{ $item->id }}"
                                            class="bg-gray-50/50 pl-16 divide-y divide-gray-100 hidden">
                                            @foreach ($item->children as $child)
                                                <div
                                                    class="p-3 flex items-center gap-4 hover:bg-gray-100 transition-colors">
                                                    <div
                                                        class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-gray-400 border border-gray-100">
                                                        <i class="{{ $child->icon }} text-xs"></i>
                                                    </div>
                                                    <div class="flex-1">
                                                        <h5 class="text-xs font-bold text-gray-700">{{ $child->label }}
                                                        </h5>
                                                        <p class="text-[10px] text-gray-400 font-medium">
                                                            {{ $child->route }}</p>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <button onclick="toggleVisibility({{ $child->id }})"
                                                            id="toggle-{{ $child->id }}"
                                                            class="px-2 py-1 rounded-md text-[9px] font-bold transition-all {{ $child->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                                            {{ $child->is_active ? 'TAMPIL' : 'TERSEMBUNYI' }}
                                                        </button>
                                                        <form action="{{ route('settings.sidebar.delete', $child) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Hapus menu child ini?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit"
                                                                class="p-1.5 text-gray-300 hover:text-red-500 transition-colors">
                                                                <i class="fas fa-trash-can text-sm"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleVisibility(id) {
                fetch(`/settings/sidebar/${id}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(res => res.json()).then(data => {
                    if (data.success) {
                        const btn = document.getElementById(`toggle-${id}`);
                        if (data.is_active) {
                            btn.classList.remove('bg-gray-100', 'text-gray-500');
                            btn.classList.add('bg-emerald-100', 'text-emerald-700');
                            btn.innerText = 'TAMPIL';
                        } else {
                            btn.classList.remove('bg-emerald-100', 'text-emerald-700');
                            btn.classList.add('bg-gray-100', 'text-gray-500');
                            btn.innerText = 'TERSEMBUNYI';
                        }
                    }
                });
            }

            function toggleChildren(id) {
                const el = document.getElementById(`children-${id}`);
                const chevron = document.getElementById(`chevron-${id}`);
                el.classList.toggle('hidden');
                chevron.classList.toggle('rotate-180');
            }

            document.addEventListener('DOMContentLoaded', function() {
                const el = document.getElementById('sidebar-items-list');
                Sortable.create(el, {
                    handle: '.drag-handle',
                    animation: 150,
                    onEnd: function() {
                        const items = Array.from(el.querySelectorAll('.sidebar-row')).map(row => row.dataset
                            .id);
                        fetch('/settings/sidebar/sort', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                items
                            })
                        });
                    }
                });
            });
        </script>
        <style>
            .sidebar-row.sortable-ghost {
                opacity: 0.4;
                background: #f3f4f6;
            }
        </style>
    @endpush
@endsection
