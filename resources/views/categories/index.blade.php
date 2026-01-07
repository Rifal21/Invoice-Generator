@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div class="min-w-0 flex-1">
                <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">Kategori Produk</h1>
                <p class="mt-2 text-sm md:text-lg text-gray-500">Kelola kategori untuk mengorganisir produk Anda.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                <a href="{{ route('categories.create') }}"
                    class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-bold rounded-2xl text-white bg-indigo-600 shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Kategori
                </a>
            </div>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="py-4 pl-6 pr-3 text-left text-xs font-black text-gray-400 uppercase tracking-widest w-20">
                                ID</th>
                            <th scope="col"
                                class="px-3 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Nama
                                Kategori</th>
                            <th scope="col"
                                class="relative py-4 pl-3 pr-6 text-right text-xs font-black text-gray-400 uppercase tracking-widest w-40">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach ($categories as $category)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="whitespace-nowrap py-5 pl-6 pr-3 text-sm font-bold text-gray-400">
                                    {{ $category->id }}</td>
                                <td class="whitespace-nowrap px-3 py-5 text-sm font-bold text-gray-900">
                                    {{ $category->name }}</td>
                                <td class="relative whitespace-nowrap py-5 pl-3 pr-6 text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('categories.edit', $category->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-lg transition-colors">Edit</a>
                                        <button type="button"
                                            class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1.5 rounded-lg transition-colors"
                                            onclick="deleteCategory({{ $category->id }})">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-4">
            @foreach ($categories as $category)
                <div
                    class="bg-white rounded-3xl p-5 shadow-lg border border-gray-100 relative overflow-hidden flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1 block">ID:
                            {{ $category->id }}</span>
                        <h3 class="text-lg font-black text-gray-900">{{ $category->name }}</h3>
                    </div>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('categories.edit', $category->id) }}"
                            class="text-center py-2 px-4 rounded-xl bg-amber-50 text-amber-700 font-bold text-xs">
                            Edit
                        </a>
                        <button type="button" onclick="deleteCategory({{ $category->id }})"
                            class="text-center py-2 px-4 rounded-xl bg-red-50 text-red-700 font-bold text-xs">
                            Hapus
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <form id="delete-form" action="" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function deleteCategory(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Kategori yang dihapus tidak dapat dikembalikan!",
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
                    form.action = `/categories/${id}`;
                    form.submit();
                }
            })
        }
    </script>
@endsection
