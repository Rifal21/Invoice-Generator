@extends('layouts.app')

@section('title', 'Edit Surat Jalan')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div class="min-w-0 flex-1">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('delivery-orders.index') }}"
                                class="text-xs md:text-sm font-bold text-gray-400 hover:text-indigo-600">Surat Jalan</a></li>
                        <li><svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg></li>
                        <li class="text-xs md:text-sm font-bold text-indigo-600">Edit Surat Jalan</li>
                    </ol>
                </nav>
                <h2 class="text-2xl md:text-3xl font-extrabold leading-7 text-gray-900 sm:truncate sm:tracking-tight">Edit
                    Surat Jalan</h2>
            </div>
        </div>

        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border-2 border-gray-100">
            <form action="{{ route('delivery-orders.update', $deliveryOrder->id) }}" method="POST"
                class="p-5 sm:p-10 space-y-8 md:space-y-10">
                @csrf
                @method('PUT')

                <!-- Order Details Section -->
                <div class="grid grid-cols-1 gap-y-6 gap-x-6 md:grid-cols-3 border-b border-gray-100 pb-8 md:pb-10">
                    <div class="md:col-span-1">
                        <label for="order_number" class="block text-sm font-bold text-gray-700 mb-2">Nomor Surat
                            Jalan</label>
                        <div class="relative flex items-center gap-2">
                            <input type="text" name="order_number" id="order_number"
                                value="{{ old('order_number', $deliveryOrder->order_number) }}" required readonly
                                class="block w-full rounded-2xl border-2 border-gray-100 bg-gray-50 py-3 px-4 text-gray-500 shadow-sm focus:outline-none transition-all duration-200 font-mono font-bold"
                                placeholder="Otomatis...">
                            <button type="button" onclick="toggleManualOrder()"
                                class="p-3 rounded-2xl bg-gray-100 text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all shadow-sm"
                                title="Edit Manual">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>

                    <div class="md:col-span-1">
                        <label for="date" class="block text-sm font-bold text-gray-700 mb-2">Tanggal</label>
                        <input type="date" name="date" id="date"
                            value="{{ old('date', $deliveryOrder->date->format('Y-m-d')) }}" required
                            class="block w-full rounded-2xl border-2 border-gray-200 py-3 px-3 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200 font-bold">
                    </div>

                    <div class="md:col-span-1">
                        <label for="location" class="block text-sm font-bold text-gray-700 mb-2">Lokasi (Opsional)</label>
                        <input type="text" name="location" id="location"
                            value="{{ old('location', $deliveryOrder->location) }}"
                            class="block w-full rounded-2xl border-2 border-gray-200 py-3 px-3 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200 font-bold"
                            placeholder="Ciawi, Bogor">
                    </div>

                    <div class="md:col-span-3">
                        <label for="customer_name" class="block text-sm font-bold text-gray-700 mb-2">Kepada Yth.</label>
                        <textarea name="customer_name" id="customer_name" required rows="2"
                            class="block w-full rounded-2xl border-2 border-gray-200 py-3 px-3 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200 placeholder-gray-400 font-bold"
                            placeholder="Nama Pelanggan / Alamat">{{ old('customer_name', $deliveryOrder->customer_name) }}</textarea>
                    </div>
                </div>

                <!-- Items Section -->
                <div>
                    <div
                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-2 border-b-2 border-gray-50 gap-2">
                        <h3 class="text-xl font-bold text-gray-900">Rincian Barang</h3>
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-widest">Input rincian barang yang
                            dikirim</span>
                    </div>

                    <div id="items-container" class="space-y-4">
                        <!-- Items will be added here -->
                    </div>

                    <button type="button" onclick="addItem()"
                        class="mt-8 inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 border border-transparent text-base font-bold rounded-2xl text-white bg-indigo-600 shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:-translate-y-1">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Tambah Barang
                    </button>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-4 pt-6 text-center">
                    <a href="{{ route('delivery-orders.index') }}"
                        class="w-full sm:w-auto text-center px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit"
                        class="w-full sm:w-auto px-10 py-4 border border-transparent text-lg font-black rounded-2xl text-white bg-indigo-600 shadow-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105">
                        Perbarui Surat Jalan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let itemIndex = 0;

        function toggleManualOrder() {
            const input = document.getElementById('order_number');
            if (input.readOnly) {
                input.readOnly = false;
                input.classList.remove('bg-gray-50', 'text-gray-500');
                input.classList.add('bg-white', 'text-gray-900', 'border-indigo-300');
                input.focus();
            } else {
                input.readOnly = true;
                input.classList.add('bg-gray-50', 'text-gray-500');
                input.classList.remove('bg-white', 'text-gray-900', 'border-indigo-300');
                updateOrderNumber();
            }
        }

        function updateOrderNumber() {
            const input = document.getElementById('order_number');
            if (!input.readOnly && input.value !== '') return;

            const date = document.getElementById('date').value;
            fetch(`{{ route('delivery-orders.next-number') }}?date=${date}`)
                .then(response => response.json())
                .then(data => {
                    input.value = data.order_number;
                });
        }

        function addItem(existingItem = null) {
            const container = document.getElementById('items-container');
            const itemDiv = document.createElement('div');
            itemDiv.className =
                "item-card bg-white p-4 md:p-6 rounded-3xl border-2 border-gray-100 shadow-sm hover:shadow-md transition-all duration-200 relative group";

            const quantityString = existingItem ? existingItem.quantity_string : '';
            const itemName = existingItem ? existingItem.item_name : '';
            const description = existingItem ? existingItem.description : '';

            itemDiv.innerHTML = `
                <button type="button" onclick="removeItem(this)" 
                    class="absolute -top-3 -right-3 bg-red-50 text-red-500 p-2 rounded-full shadow-sm hover:bg-red-500 hover:text-white transition-all duration-200 focus:outline-none z-10">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12 md:col-span-3">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Banyaknya</label>
                        <input type="text" name="items[${itemIndex}][quantity_string]" value="${quantityString}" required
                            class="block w-full rounded-xl border-2 border-gray-200 py-2.5 px-3 text-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold" 
                            placeholder="800 kg / 10 Karung">
                    </div>

                    <div class="col-span-12 md:col-span-4">
                         <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nama Barang</label>
                         <input type="text" name="items[${itemIndex}][item_name]" value="${itemName}" required
                            class="block w-full rounded-xl border-2 border-gray-200 py-2.5 px-3 text-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold"
                            placeholder="Beras IR 64">
                    </div>

                    <div class="col-span-12 md:col-span-5">
                         <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Keterangan</label>
                         <input type="text" name="items[${itemIndex}][description]" value="${description}"
                            class="block w-full rounded-xl border-2 border-gray-200 py-2.5 px-3 text-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold"
                            placeholder="Lunas / Titip">
                    </div>
                </div>
            `;

            container.appendChild(itemDiv);
            itemIndex++;
        }

        function removeItem(button) {
            button.closest('.item-card').remove();
        }

        document.addEventListener('DOMContentLoaded', function() {
            @if ($deliveryOrder->items->count() > 0)
                @foreach ($deliveryOrder->items as $item)
                    addItem({
                        quantity_string: "{{ $item->quantity_string }}",
                        item_name: "{{ $item->item_name }}",
                        description: "{{ $item->description }}"
                    });
                @endforeach
            @else
                addItem();
            @endif

            document.getElementById('date').addEventListener('change', function() {
                updateOrderNumber();
            });
        });
    </script>
@endsection
