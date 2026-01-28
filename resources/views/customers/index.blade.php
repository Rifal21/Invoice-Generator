@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div class="min-w-0 flex-1">
                <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">Daftar Pelanggan</h1>
                <p class="mt-2 text-sm md:text-lg text-gray-500">Kelola data pelanggan Anda.</p>
            </div>
            <div class="flex shrink-0">
                <a href="{{ route('customers.create') }}"
                    class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-bold rounded-2xl text-white bg-indigo-600 shadow-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:-translate-y-1">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Pelanggan
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white shadow-lg rounded-3xl p-5 mb-6 border border-gray-100">
            <form action="{{ route('customers.index') }}" method="GET"
                class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-10">
                    <label for="search" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Cari
                        Pelanggan</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Nama, Telepon, atau Email..."
                        class="block w-full rounded-2xl border-gray-200 py-2.5 px-3 text-sm text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                <div class="md:col-span-2">
                    <button type="submit"
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-2xl shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                        Cari
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                Nama</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                Kontak</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                Alamat</th>
                            <th scope="col" class="relative px-6 py-4">
                                <span class="sr-only">Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($customers as $customer)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $customer->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $customer->description }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $customer->phone ?? '-' }}</div>
                                    <div class="text-sm text-gray-500">{{ $customer->email ?? '-' }}</div>
                                    @if ($customer->telegram_chat_id)
                                        <div class="text-xs text-indigo-600 font-bold mt-1">TG:
                                            {{ $customer->telegram_chat_id }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 line-clamp-2 md:whitespace-pre-wrap">
                                        {{ $customer->address ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('customers.edit', $customer) }}"
                                            class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-lg transition-colors">Edit</a>

                                        <form action="{{ route('customers.destroy', $customer) }}" method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1.5 rounded-lg transition-colors">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">
                                    Belum ada data pelanggan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $customers->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection
