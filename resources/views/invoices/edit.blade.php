@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="min-w-0 flex-1">
                <h2 class="text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight">Edit Invoice
                    {{ $invoice->invoice_number }}</h2>
                <p class="mt-1 text-sm text-gray-500">Update the invoice details below.</p>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <form action="{{ route('invoices.update', $invoice) }}" method="POST" class="p-8 space-y-8">
                @csrf
                @method('PUT')

                <!-- Invoice Details Section -->
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6 border-b border-gray-200 pb-8">

                    <div class="sm:col-span-2">
                        <label for="date" class="block text-sm font-medium leading-6 text-gray-700">Date</label>
                        <div class="mt-2">
                            <input type="date" name="date" id="date" value="{{ $invoice->date }}" required
                                class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="customer_name" class="block text-sm font-medium leading-6 text-gray-700">Customer
                            Name</label>
                        <div class="mt-2">
                            <input type="text" name="customer_name" id="customer_name"
                                value="{{ $invoice->customer_name }}" required placeholder="Enter customer name"
                                class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>
                </div>

                <!-- Items Section -->
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900">Invoice Items</h3>
                    </div>

                    <div class="ring-1 ring-gray-200 rounded-lg overflow-hidden">
                        <table id="items-table" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 w-1/3">
                                        Product</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 w-32">Price</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 w-24">Qty</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 w-24">Unit</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 w-32">Total</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 w-16">
                                        <span class="sr-only">Remove</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <!-- Items will be added here -->
                            </tbody>
                        </table>
                    </div>
                    <button type="button" onclick="addItem()"
                        class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 mt-4">
                        +
                        Add Item
                    </button>
                </div>

                <!-- Totals Section -->
                <div class="border-t border-gray-200 pt-8">
                    <div class="flex justify-end">
                        <div class="w-full sm:w-1/3 space-y-4">
                            <div class="flex justify-between items-center text-xl font-bold text-gray-900">
                                <span>Total Amount:</span>
                                <span>Rp <span id="grand-total">0.00</span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-x-4 border-t border-gray-200 pt-6">
                    <a href="{{ route('invoices.index') }}"
                        class="text-sm font-semibold leading-6 text-gray-900 hover:text-gray-700">Cancel</a>
                    <button type="submit"
                        class="rounded-md bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all duration-200">
                        Update Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let itemIndex = 0;
        const products = @json($products);
        const existingItems = @json($invoice->items);

        function addItem(existingItem = null) {
            const table = document.getElementById('items-table').getElementsByTagName('tbody')[0];
            const row = table.insertRow();
            row.className = "hover:bg-gray-50 transition-colors duration-150";

            let productOptions = '<option value="">Select Product</option>';
            products.forEach(product => {
                const selected = existingItem && existingItem.product_id == product.id ? 'selected' : '';
                productOptions +=
                    `<option value="${product.id}" data-price="${product.price}" data-unit="${product.unit}" ${selected}>${product.name}</option>`;
            });

            const selectId = `product-select-${itemIndex}`;
            const quantity = existingItem ? existingItem.quantity : 1;
            const price = existingItem ? existingItem.price : '';
            const unit = existingItem ? existingItem.unit : '';
            const total = existingItem ? existingItem.total : '';

            row.innerHTML = `
                <td class="py-4 pl-4 pr-3 sm:pl-6">
                    <select id="${selectId}" name="items[${itemIndex}][product_id]" class="product-select block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" required style="width: 100%">
                        ${productOptions}
                    </select>
                </td>
                <td class="px-3 py-4">
                    <div class="relative rounded-md shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="items[${itemIndex}][price]" class="price-input block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-gray-50" readonly value="${price}" step="0.01" onchange="updateTotal(this)">
                    </div>
                </td>
                <td class="px-3 py-4">
                    <input type="number" name="items[${itemIndex}][quantity]" class="quantity-input block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 text-center" min="1" value="${quantity}" onchange="updateTotal(this)" required>
                </td>
                <td class="px-3 py-4">
                    <input type="text" name="items[${itemIndex}][unit]" class="unit-input block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 text-center bg-gray-50" readonly value="${unit}">
                </td>
                <td class="px-3 py-4">
                    <div class="relative rounded-md shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" class="total-input block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-gray-50 font-medium" readonly value="${total}">
                    </div>
                </td>
                <td class="relative py-4 pl-3 pr-4 sm:pr-6 text-center">
                    <button type="button" class="text-red-400 hover:text-red-600 transition-colors duration-200" onclick="removeItem(this)">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </td>
            `;

            // Initialize Select2 with tags: true
            $(`#${selectId}`).select2({
                placeholder: "Select or type a product",
                allowClear: true,
                tags: true,
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        newTag: true // add additional parameters
                    }
                }
            });

            // Handle Select2 change event
            $(`#${selectId}`).on('select2:select', function(e) {
                const selectElement = this;
                const data = e.params.data;
                const row = selectElement.closest('tr');
                const priceInput = row.querySelector('.price-input');
                const unitInput = row.querySelector('.unit-input');

                if (data.newTag) {
                    // It's a new product
                    priceInput.value = '';
                    priceInput.readOnly = false;
                    priceInput.classList.remove('bg-gray-50');

                    unitInput.value = '';
                    unitInput.readOnly = false;
                    unitInput.classList.remove('bg-gray-50');
                    unitInput.placeholder = 'e.g. kg, pcs';
                } else {
                    // It's an existing product
                    const selectedOption = selectElement.options[selectElement.selectedIndex];
                    const price = selectedOption.getAttribute('data-price');
                    const unit = selectedOption.getAttribute('data-unit');

                    priceInput.value = price;
                    priceInput.readOnly = true;
                    priceInput.classList.add('bg-gray-50');

                    unitInput.value = unit;
                    unitInput.readOnly = true;
                    unitInput.classList.add('bg-gray-50');
                }
                updateTotal(selectElement);
            });

            // Handle Select2 clear event
            $(`#${selectId}`).on('select2:clear', function(e) {
                const row = this.closest('tr');
                const priceInput = row.querySelector('.price-input');
                const unitInput = row.querySelector('.unit-input');

                priceInput.value = '';
                priceInput.readOnly = true;
                priceInput.classList.add('bg-gray-50');

                unitInput.value = '';
                unitInput.readOnly = true;
                unitInput.classList.add('bg-gray-50');

                row.querySelector('.total-input').value = '';
                calculateGrandTotal();
            });

            itemIndex++;
        }

        function updateTotal(element) {
            const row = element.closest('tr');
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
            const total = price * quantity;
            row.querySelector('.total-input').value = total.toFixed(2);
            calculateGrandTotal();
        }

        function removeItem(button) {
            const row = button.closest('tr');

            // Try to destroy Select2 instance, but don't let it stop row removal if it fails
            try {
                const select = row.querySelector('.product-select');
                if (select && $(select).data('select2')) {
                    $(select).select2('destroy');
                }
            } catch (e) {
                console.error('Error destroying Select2:', e);
            }

            row.remove();
            calculateGrandTotal();
        }

        function calculateGrandTotal() {
            let grandTotal = 0;
            document.querySelectorAll('.total-input').forEach(input => {
                grandTotal += parseFloat(input.value) || 0;
            });
            // Format with commas for thousands
            document.getElementById('grand-total').innerText = grandTotal.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Add existing items or initial item
        $(document).ready(function() {
            if (existingItems.length > 0) {
                existingItems.forEach(item => {
                    addItem(item);
                });
                calculateGrandTotal();
            } else {
                addItem();
            }
        });
    </script>
@endsection
