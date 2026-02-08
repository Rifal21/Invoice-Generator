@extends('layouts.app')

@section('title', 'Upload Nota')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <!-- Breadcrumb & Back Button -->
        <div class="mb-8 flex items-center justify-between">
            <a href="{{ route('supplier-notas.index') }}"
                class="inline-flex items-center text-sm font-black text-indigo-600 hover:text-indigo-800 transition-colors bg-indigo-50 px-5 py-2.5 rounded-2xl group">
                <i class="fas fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform"></i>
                Kembali ke Daftar
            </a>
        </div>

        <div class="mb-10 text-center md:text-left">
            <h1 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tight">Upload Nota Transaksi</h1>
            <p class="mt-3 text-lg text-gray-500 font-medium italic">Simpan bukti transaksi supplier dalam format digital
                yang aman.</p>
        </div>

        <div class="relative">
            <!-- Decorative Elements -->
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-500/5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-pink-500/5 rounded-full blur-3xl"></div>

            <div
                class="bg-white shadow-[0_20px_50px_rgba(79,70,229,0.1)] rounded-[3rem] overflow-hidden border border-gray-100 relative z-10 transition-all hover:shadow-[0_25px_60px_rgba(79,70,229,0.15)]">
                <form action="{{ route('supplier-notas.store') }}" method="POST" enctype="multipart/form-data"
                    class="p-8 md:p-12">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                        <div class="space-y-3">
                            <label for="supplier_id"
                                class="flex items-center text-xs font-black text-gray-400 uppercase tracking-[0.2em] ml-2">
                                <i class="fas fa-truck text-indigo-500 mr-2"></i> Pilih Supplier
                            </label>
                            <div class="relative">
                                <select name="supplier_id" id="supplier_id" required
                                    class="select2 block w-full rounded-2xl border-gray-100 bg-gray-50/50 py-4 px-5 text-sm font-bold text-gray-900 shadow-inner focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all appearance-none cursor-pointer">
                                    <option value="">Pilih Mitra Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}"
                                            {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                            @error('supplier_id')
                                <p class="text-rose-500 text-[10px] font-black mt-1 ml-2 uppercase tracking-widest">
                                    {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-3">
                            <label for="nota_number"
                                class="flex items-center text-xs font-black text-gray-400 uppercase tracking-[0.2em] ml-2">
                                <i class="fas fa-hashtag text-indigo-500 mr-2"></i> Nomor Nota
                            </label>
                            <input type="text" name="nota_number" id="nota_number" value="{{ old('nota_number') }}"
                                required placeholder="Contoh: INV/JR/2024/001"
                                class="block w-full rounded-2xl border-gray-100 bg-gray-50/50 py-4 px-5 text-sm font-bold text-gray-900 shadow-inner focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                            @error('nota_number')
                                <p class="text-rose-500 text-[10px] font-black mt-1 ml-2 uppercase tracking-widest">
                                    {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-3">
                            <label for="transaction_date"
                                class="flex items-center text-xs font-black text-gray-400 uppercase tracking-[0.2em] ml-2">
                                <i class="fas fa-calendar-alt text-indigo-500 mr-2"></i> Tanggal Transaksi
                            </label>
                            <input type="date" name="transaction_date" id="transaction_date"
                                value="{{ old('transaction_date', date('Y-m-d')) }}" required
                                class="block w-full rounded-2xl border-gray-100 bg-gray-50/50 py-4 px-5 text-sm font-bold text-gray-900 shadow-inner focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                            @error('transaction_date')
                                <p class="text-rose-500 text-[10px] font-black mt-1 ml-2 uppercase tracking-widest">
                                    {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-3">
                            <label for="total_amount"
                                class="flex items-center text-xs font-black text-gray-400 uppercase tracking-[0.2em] ml-2">
                                <i class="fas fa-money-bill-wave text-indigo-500 mr-2"></i> Total Nominal (Rp)
                            </label>
                            <div class="relative">
                                <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 font-black text-sm">Rp
                                </div>
                                <input type="number" name="total_amount" id="total_amount"
                                    value="{{ old('total_amount') }}" required placeholder="0"
                                    class="block w-full rounded-2xl border-gray-100 bg-gray-50/50 py-4 pl-12 pr-5 text-sm font-black text-gray-900 shadow-inner focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                            </div>
                            @error('total_amount')
                                <p class="text-rose-500 text-[10px] font-black mt-1 ml-2 uppercase tracking-widest">
                                    {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2 space-y-4">
                            <label
                                class="flex items-center text-xs font-black text-gray-400 uppercase tracking-[0.2em] ml-2">
                                <i class="fas fa-file-export text-indigo-500 mr-2"></i> Unggah File Nota (PDF/Gambar)
                            </label>
                            <div id="drop-zone" class="relative group cursor-pointer">
                                <input id="file-upload" name="file" type="file" class="sr-only" required
                                    accept=".pdf,.jpg,.jpeg,.png">
                                <div
                                    class="flex flex-col items-center justify-center py-12 px-6 border-4 border-dashed border-gray-100 rounded-[2.5rem] bg-gray-50/30 transition-all group-hover:bg-indigo-50/50 group-hover:border-indigo-200">
                                    <div
                                        class="w-20 h-20 rounded-3xl bg-white shadow-lg flex items-center justify-center text-indigo-500 mb-4 transition-transform group-hover:scale-110 group-hover:rotate-6">
                                        <i class="fas fa-cloud-arrow-up text-3xl"></i>
                                    </div>
                                    <p class="text-sm font-black text-gray-700">Tarik dan lepas file di sini</p>
                                    <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-widest">atau klik
                                        untuk menelusuri</p>

                                    <div id="file-info"
                                        class="hidden mt-6 px-6 py-3 rounded-2xl bg-white shadow-xl flex items-center gap-3 animate-bounce">
                                        <i class="fas fa-check-circle text-emerald-500"></i>
                                        <span id="file-name" class="text-xs font-black text-gray-700"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-between px-2">
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter italic">* Maksimal
                                    ukuran file 5MB</p>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter italic">* Format:
                                    PDF, PNG, JPG</p>
                            </div>
                            @error('file')
                                <p class="text-rose-500 text-[10px] font-black mt-1 ml-2 uppercase tracking-widest">
                                    {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2 space-y-3">
                            <label for="description"
                                class="flex items-center text-xs font-black text-gray-400 uppercase tracking-[0.2em] ml-2">
                                <i class="fas fa-comment-alt text-indigo-500 mr-2"></i> Keterangan Tambahan
                            </label>
                            <textarea name="description" id="description" rows="4" placeholder="Tambahkan catatan jika diperlukan..."
                                class="block w-full rounded-3xl border-gray-100 bg-gray-50/50 py-4 px-6 text-sm font-medium text-gray-900 shadow-inner focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-rose-500 text-[10px] font-black mt-1 ml-2 uppercase tracking-widest">
                                    {{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div
                        class="flex flex-col sm:flex-row items-center justify-between gap-6 pt-6 border-t border-gray-50 leading-none">
                        <div class="flex items-center gap-3 text-emerald-600">
                            <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center">
                                <i class="fas fa-shield-halved text-xs"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest">Data Terenkripsi & Aman</span>
                        </div>
                        <div class="flex items-center gap-4 w-full sm:w-auto">
                            <a href="{{ route('supplier-notas.index') }}"
                                class="flex-1 sm:flex-none text-center px-8 py-4 text-sm font-black text-gray-400 hover:text-gray-900 transition-colors uppercase tracking-widest">
                                Batal
                            </a>
                            <button type="submit"
                                class="flex-1 sm:flex-none px-12 py-5 bg-gray-900 text-white font-black rounded-[1.5rem] shadow-2xl hover:bg-black focus:outline-none focus:ring-4 focus:ring-gray-300 transition-all transform active:scale-95 shadow-gray-200">
                                Simpan Transaksi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const dropZone = document.getElementById('drop-zone');
        const fileUpload = document.getElementById('file-upload');
        const fileInfo = document.getElementById('file-info');
        const fileName = document.getElementById('file-name');

        dropZone.addEventListener('click', () => fileUpload.click());

        fileUpload.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                updateFileInfo(e.target.files[0].name);
            }
        });

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.querySelector('.border-dashed').classList.add('bg-indigo-50', 'border-indigo-300');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.querySelector('.border-dashed').classList.remove('bg-indigo-50', 'border-indigo-300');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.querySelector('.border-dashed').classList.remove('bg-indigo-50', 'border-indigo-300');

            if (e.dataTransfer.files.length > 0) {
                fileUpload.files = e.dataTransfer.files;
                updateFileInfo(e.dataTransfer.files[0].name);
            }
        });

        function updateFileInfo(name) {
            fileInfo.classList.remove('hidden');
            fileName.textContent = name;
        }
    </script>

    @push('scripts')
        <style>
            .select2-container--default .select2-selection--single {
                background-color: rgba(249, 250, 251, 0.5) !important;
                border-color: #f3f4f6 !important;
                border-radius: 1rem !important;
                height: 58px !important;
                display: flex !important;
                align-items: center !important;
                box-shadow: inset 0 2px 4px 0 rgb(0 0 0 / 0.05) !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 58px !important;
                padding-left: 1.25rem !important;
                font-weight: 700 !important;
                color: #111827 !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 56px !important;
                right: 1.25rem !important;
            }

            .select2-dropdown {
                border-radius: 1.5rem !important;
                border: 1px solid #f3f4f6 !important;
                box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1) !important;
                overflow: hidden !important;
            }
        </style>
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    tags: true,
                    placeholder: "Pilih atau Ketik Supplier Baru",
                    allowClear: true,
                    width: '100%',
                    theme: "default"
                });
            });
        </script>
    @endpush
@endsection
