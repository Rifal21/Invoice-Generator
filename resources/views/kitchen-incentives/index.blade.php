@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div>
                <h2 class="text-3xl font-black text-gray-900 tracking-tight">Invoice Insentif Dapur</h2>
                <p class="mt-1 text-sm text-gray-500">Kelola daftar invoice insentif dapur Anda.</p>
            </div>
            <div class="flex shrink-0">
                <a href="{{ route('kitchen-incentives.create') }}"
                    class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-bold rounded-2xl text-white bg-indigo-600 shadow-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-1">
                    <i class="fas fa-plus mr-2"></i> Buat Baru
                </a>
            </div>
        </div>

        <!-- Mobile Card View (Visible on small screens) -->
        <div class="grid grid-cols-1 gap-4 md:hidden">
            @forelse($invoices as $invoice)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-bl-full -mr-12 -mt-12 opacity-50">
                    </div>

                    <div class="flex justify-between items-start mb-4 relative z-10">
                        <div>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-800 mb-2">
                                {{ $invoice->invoice_number }}
                            </span>
                            <h3 class="font-bold text-gray-900 line-clamp-1">{{ $invoice->recipient_name }}</h3>
                            <p class="text-xs text-gray-400 mt-1">
                                <i class="far fa-clock mr-1"></i> {{ $invoice->items->first()->duration_text ?? '-' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-black text-indigo-600">Rp
                                {{ number_format($invoice->total_amount, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-2 border-t border-gray-100 pt-4 mt-2">
                        <a href="{{ route('kitchen-incentives.edit', $invoice->id) }}"
                            class="flex flex-col items-center justify-center py-2 rounded-xl text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors">
                            <i class="fas fa-edit mb-1"></i>
                            <span class="text-[10px] font-bold">Edit</span>
                        </a>
                        <a href="{{ route('kitchen-incentives.show', $invoice->id) }}" target="_blank"
                            class="flex flex-col items-center justify-center py-2 rounded-xl text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                            <i class="fas fa-eye mb-1"></i>
                            <span class="text-[10px] font-bold">Lihat</span>
                        </a>
                        <a href="{{ route('kitchen-incentives.export-pdf', $invoice->id) }}"
                            class="flex flex-col items-center justify-center py-2 rounded-xl text-green-600 bg-green-50 hover:bg-green-100 transition-colors">
                            <i class="fas fa-download mb-1"></i>
                            <span class="text-[10px] font-bold">Unduh</span>
                        </a>
                        <button onclick="confirmDelete('{{ $invoice->id }}')"
                            class="flex flex-col items-center justify-center py-2 rounded-xl text-red-600 bg-red-50 hover:bg-red-100 transition-colors">
                            <i class="fas fa-trash-alt mb-1"></i>
                            <span class="text-[10px] font-bold">Hapus</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-8 text-center text-gray-400">
                    <i class="fas fa-file-invoice text-4xl mb-3 opacity-50"></i>
                    <p class="text-sm font-medium">Belum ada invoice insentif dapur.</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table View (Visible on medium screens and up) -->
        <div class="hidden md:block bg-white shadow-xl rounded-3xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">No.
                                Invoice</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                Periode</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                Penerima</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-400 uppercase tracking-widest">
                                Total</th>
                            <th class="px-6 py-4 text-center text-xs font-black text-gray-400 uppercase tracking-widest">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($invoices as $invoice)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                    <span
                                        class="bg-gray-100 text-gray-600 py-1 px-3 rounded-lg group-hover:bg-indigo-100 group-hover:text-indigo-600 transition-colors">
                                        {{ $invoice->invoice_number }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">
                                    {{ $invoice->items->first()->duration_text ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                    {{ $invoice->recipient_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-black">
                                    Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                    <div
                                        class="flex justify-center items-center gap-3 opacity-0 group-hover:opacity-100 transition-all duration-200 transform translate-y-1 group-hover:translate-y-0">
                                        <a href="{{ route('kitchen-incentives.edit', $invoice->id) }}"
                                            class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('kitchen-incentives.show', $invoice->id) }}"
                                            class="p-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors"
                                            target="_blank" title="Lihat PDF">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('kitchen-incentives.export-pdf', $invoice->id) }}"
                                            class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors"
                                            title="Download PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button type="button" onclick="confirmDelete('{{ $invoice->id }}')"
                                            class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                                            title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                    <!-- Fallback for touch devices where hover isn't standard -->
                                    <div class="md:hidden flex justify-center items-center gap-3">
                                        <a href="{{ route('kitchen-incentives.edit', $invoice->id) }}"
                                            class="text-blue-600"><i class="fas fa-edit"></i></a>
                                        <!-- ... (simplified mobile actions if needed, but the card view handles mobile) ... -->
                                    </div>

                                    <form id="delete-form-{{ $invoice->id }}"
                                        action="{{ route('kitchen-incentives.destroy', $invoice->id) }}" method="POST"
                                        class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                    <i class="fas fa-box-open text-4xl mb-3 opacity-30"></i>
                                    <p>Belum ada invoice yang dibuat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Invoice?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-6 py-2.5 font-bold',
                    cancelButton: 'rounded-xl px-6 py-2.5 font-bold text-gray-700'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>
@endsection
