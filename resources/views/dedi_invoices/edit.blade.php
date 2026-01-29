@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Nota Faktur H Dedi</h2>

        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
            <form action="{{ route('dedi-invoices.update', $dedi_invoice->id) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nomor Invoice</label>
                        <input type="text" name="invoice_number"
                            value="{{ old('invoice_number', $dedi_invoice->invoice_number) }}"
                            class="w-full rounded-xl border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 font-bold"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal</label>
                        <input type="date" name="date" value="{{ old('date', $dedi_invoice->date) }}"
                            class="w-full rounded-xl border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 font-bold"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Pelanggan / Kepada Yth</label>
                        <input type="text" name="customer_name"
                            value="{{ old('customer_name', $dedi_invoice->customer_name) }}"
                            class="w-full rounded-xl border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 font-bold"
                            placeholder="Contoh: Rhiezky" required>
                    </div>
                </div>

                <!-- Items -->
                <div class="border-t border-gray-100 pt-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Item Barang</h3>
                    <div id="items-container" class="space-y-4">
                        <!-- Dynamic Items -->
                        @foreach ($dedi_invoice->items as $index => $item)
                            <div class="grid grid-cols-12 gap-4 items-end bg-gray-50 p-4 rounded-xl relative">
                                <div class="col-span-12 md:col-span-5">
                                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Nama Barang</label>
                                    <input type="text" name="items[{{ $index }}][item_name]"
                                        value="{{ $item->item_name }}"
                                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Nama Barang" required>
                                </div>
                                <div class="col-span-6 md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Banyaknya</label>
                                    <input type="number" step="0.01" name="items[{{ $index }}][quantity]"
                                        value="{{ $item->quantity }}"
                                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Qty" required>
                                </div>
                                <div class="col-span-6 md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Satuan</label>
                                    <input type="text" name="items[{{ $index }}][unit]"
                                        value="{{ $item->unit }}"
                                        class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="kg, pcs..." required>
                                </div>
                                <div class="col-span-12 md:col-span-3">
                                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Harga Satuan</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                        <input type="number" step="0.01" name="items[{{ $index }}][price]"
                                            value="{{ $item->price }}"
                                            class="w-full pl-8 rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                            placeholder="0" required>
                                    </div>
                                </div>
                                <button type="button" onclick="this.parentElement.remove()"
                                    class="absolute -top-2 -right-2 bg-red-100 text-red-600 rounded-full p-1 shadow-sm hover:bg-red-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" onclick="addItem()"
                        class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 font-bold rounded-xl hover:bg-indigo-100 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Item
                    </button>
                </div>

                <div class="border-t border-gray-100 pt-6 flex justify-end">
                    <button type="submit"
                        class="px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-lg">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let itemIndex = {{ $dedi_invoice->items->count() }};

        function addItem() {
            const container = document.getElementById('items-container');
            const div = document.createElement('div');
            div.className = 'grid grid-cols-12 gap-4 items-end bg-gray-50 p-4 rounded-xl relative';
            div.innerHTML = `
            <div class="col-span-12 md:col-span-5">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Nama Barang</label>
                <input type="text" name="items[${itemIndex}][item_name]" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nama Barang" required>
            </div>
             <div class="col-span-6 md:col-span-2">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Banyaknya</label>
                <input type="number" step="0.01" name="items[${itemIndex}][quantity]" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Qty" required>
            </div>
            <div class="col-span-6 md:col-span-2">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Satuan</label>
                <input type="text" name="items[${itemIndex}][unit]" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="kg, pcs..." required>
            </div>
            <div class="col-span-12 md:col-span-3">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Harga Satuan</label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                    <input type="number" step="0.01" name="items[${itemIndex}][price]" class="w-full pl-8 rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0" required>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="absolute -top-2 -right-2 bg-red-100 text-red-600 rounded-full p-1 shadow-sm hover:bg-red-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        `;
            container.appendChild(div);
            itemIndex++;
        }
    </script>
@endsection
