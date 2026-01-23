@extends('layouts.app')

@section('title', 'Daftar Surat Jalan')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div class="min-w-0 flex-1">
                <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">Surat Jalan</h1>
                <p class="mt-2 text-sm md:text-lg text-gray-500">Kelola dan pantau semua surat jalan Anda.</p>
            </div>
            <div class="flex shrink-0">
                <a href="{{ route('delivery-orders.create') }}"
                    class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-bold rounded-2xl text-white bg-indigo-600 shadow-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:-translate-y-1">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Buat Surat Jalan Baru
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <form id="filter-form" action="{{ route('delivery-orders.index') }}" method="GET"
            class="bg-white shadow-lg rounded-3xl p-5 mb-6 border border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-y-4 gap-x-6 items-end">
                <div class="md:col-span-6">
                    <label for="search"
                        class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Cari</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="No. Surat Jalan / Nama Pelanggan..."
                        class="block w-full rounded-2xl border-gray-200 py-2.5 px-3 text-sm text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>

                <div class="md:col-span-3">
                    <label for="date"
                        class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal</label>
                    <input type="date" name="date" id="date" value="{{ request('date') }}"
                        class="block w-full rounded-2xl border-gray-200 py-2.5 px-3 text-sm text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>

                <div class="md:col-span-3 flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 text-white font-bold py-2.5 px-4 rounded-2xl hover:bg-indigo-700 transition-all shadow-md text-sm">
                        Filter
                    </button>
                    <a href="{{ route('delivery-orders.index') }}"
                        class="bg-gray-100 text-gray-600 font-bold py-2.5 px-4 rounded-2xl hover:bg-gray-200 transition-all text-center text-sm flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Desktop Table View -->
        <div class="hidden md:block bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100 mb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th scope="col"
                                class="px-6 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                Nomor Surat Jalan</th>
                            <th scope="col"
                                class="px-3 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                Tanggal</th>
                            <th scope="col"
                                class="px-3 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                Kepada</th>
                            <th scope="col"
                                class="px-3 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                Lokasi</th>
                            <th scope="col"
                                class="px-6 py-5 text-right text-xs font-black text-gray-400 uppercase tracking-widest">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        @forelse ($deliveryOrders as $order)
                            <tr class="hover:bg-indigo-50/30 transition-colors duration-150 group">
                                <td class="whitespace-nowrap px-6 py-5">
                                    <div class="flex items-center">
                                        <div
                                            class="h-10 w-10 flex-shrink-0 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600">
                                            <i class="fas fa-truck"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900">
                                                {{ $order->order_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-600 font-medium">
                                    {{ \Carbon\Carbon::parse($order->date)->format('d/m/Y') }}
                                </td>
                                <td class="px-3 py-5">
                                    <div class="text-sm font-bold text-gray-900 line-clamp-1">
                                        {{ Str::limit($order->customer_name, 50) }}</div>
                                </td>
                                <td class="px-3 py-5">
                                    <div class="text-sm text-gray-600">
                                        {{ $order->location ?? '-' }}</div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-5 text-right text-sm font-bold space-x-2">
                                    <a href="{{ route('delivery-orders.show', $order->id) }}"
                                        class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-lg transition-colors inline-block">Lihat</a>
                                    <a href="{{ route('delivery-orders.edit', $order->id) }}"
                                        class="text-amber-600 hover:text-amber-900 bg-amber-50 px-3 py-1.5 rounded-lg transition-colors inline-block">Edit</a>
                                    <button type="button" onclick="deleteOrder({{ $order->id }})"
                                        class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1.5 rounded-lg transition-colors inline-block">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-truck-loading text-5xl text-gray-200 mb-4"></i>
                                        <p class="text-gray-500 text-lg font-medium">Belum ada surat jalan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-4 mb-6">
            @forelse($deliveryOrders as $order)
                <div class="bg-white rounded-3xl p-5 shadow-lg border border-gray-100 relative overflow-hidden">
                    <div class="flex items-start gap-4 mb-4">
                        <div
                            class="h-12 w-12 shrink-0 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-600">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900">{{ $order->order_number }}</h3>
                            <p class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($order->date)->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Kepada</p>
                            <p class="text-base font-bold text-gray-900 line-clamp-2">{{ $order->customer_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Lokasi</p>
                            <p class="text-sm text-gray-600">{{ $order->location ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <a href="{{ route('delivery-orders.show', $order->id) }}"
                            class="flex items-center justify-center py-2 px-3 rounded-xl bg-indigo-50 text-indigo-700 font-bold text-sm text-center">
                            Detail
                        </a>
                        <a href="{{ route('delivery-orders.edit', $order->id) }}"
                            class="flex items-center justify-center py-2 px-3 rounded-xl bg-amber-50 text-amber-700 font-bold text-sm text-center">
                            Edit
                        </a>
                        <button type="button" onclick="deleteOrder({{ $order->id }})"
                            class="flex items-center justify-center py-2 px-3 rounded-xl bg-red-50 text-red-700 font-bold text-sm text-center">
                            Hapus
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-white rounded-3xl border-2 border-dashed border-gray-200">
                    <i class="fas fa-truck-loading text-4xl text-gray-200 mb-3"></i>
                    <p class="text-gray-500 font-medium">Belum ada surat jalan</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $deliveryOrders->links() }}
        </div>

        <form id="delete-form" action="" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>

    <script>
        function deleteOrder(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Surat jalan yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    container: 'rounded-3xl',
                    popup: 'rounded-3xl',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('delete-form');
                    form.action = `/delivery-orders/${id}`;
                    form.submit();
                }
            })
        }
    </script>
@endsection
