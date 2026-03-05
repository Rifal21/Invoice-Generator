@extends('layouts.app')

@section('title', 'Laporan Pemeriksaan')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tight">Arsip Laporan</h1>
                <p class="mt-2 text-sm text-gray-500 font-medium italic">Laporan pemeriksaan berkala untuk setiap pelanggan
                    koperasi.</p>
            </div>
            <div class="mt-6 sm:ml-16 sm:mt-0 sm:flex-none">
                <a href="{{ route('examination-reports.create') }}"
                    class="group relative inline-flex items-center gap-3 overflow-hidden rounded-2xl bg-indigo-600 px-8 py-4 text-center text-sm font-black text-white shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all hover:scale-105 active:scale-95">
                    <i class="fas fa-file-signature text-lg group-hover:rotate-12 transition-transform"></i>
                    <span class="uppercase tracking-widest">Buat Laporan Baru</span>
                </a>
            </div>
        </div>

        <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($reports as $report)
                <div
                    class="group bg-white rounded-[2.5rem] shadow-sm border border-gray-100 hover:shadow-2xl hover:border-indigo-100 transition-all duration-500 overflow-hidden flex flex-col relative">
                    <!-- Date Badge -->
                    <div class="absolute top-6 right-6">
                        <div
                            class="bg-indigo-50/50 backdrop-blur-sm px-4 py-2 rounded-2xl border border-indigo-100/50 text-center shadow-inner">
                            <span
                                class="block text-lg font-black text-indigo-700 leading-none">{{ \Carbon\Carbon::parse($report->report_date)->format('d') }}</span>
                            <span
                                class="block text-[8px] font-black uppercase text-indigo-400 tracking-tighter">{{ \Carbon\Carbon::parse($report->report_date)->format('M Y') }}</span>
                        </div>
                    </div>

                    <div class="p-8 pb-4 flex-1">
                        <div class="flex items-center gap-4 mb-6">
                            <div
                                class="w-14 h-14 bg-indigo-50 group-hover:bg-indigo-600 rounded-2xl flex items-center justify-center text-indigo-600 group-hover:text-white group-hover:rotate-6 transition-all duration-500 shadow-inner group-hover:shadow-lg">
                                @php
                                    $extension = strtolower(pathinfo($report->file_path, PATHINFO_EXTENSION));
                                    $icon = in_array($extension, ['pdf']) ? 'fa-file-pdf' : 'fa-file-image';
                                @endphp
                                <i class="fas {{ $icon }} text-2xl"></i>
                            </div>
                            <div class="max-w-[calc(100%-4.5rem)]">
                                <span
                                    class="px-2 py-0.5 bg-indigo-100/50 text-[10px] font-black uppercase text-indigo-700 rounded-lg group-hover:bg-indigo-200/50">
                                    {{ $report->customer->name ?? 'UMUM' }}
                                </span>
                                <h3
                                    class="mt-1 text-base font-black text-gray-900 leading-tight truncate group-hover:text-indigo-900 transition-colors">
                                    Berkas Laporan</h3>
                            </div>
                        </div>

                        @if ($report->description)
                            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100/50 min-h-[4.5rem]">
                                <p class="text-xs text-gray-500 leading-relaxed line-clamp-2">
                                    {{ $report->description }}
                                </p>
                            </div>
                        @else
                            <div
                                class="min-h-[4.5rem] flex items-center justify-center border-2 border-dashed border-gray-50 rounded-2xl">
                                <span class="text-[10px] text-gray-300 font-bold uppercase tracking-widest italic">Tidak ada
                                    keterangan</span>
                            </div>
                        @endif

                        <div class="mt-6 flex items-center justify-between border-t border-gray-50 pt-4">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-5 h-5 rounded-md bg-gray-100 flex items-center justify-center text-[10px] text-gray-400">
                                    <i class="fas fa-user-edit"></i>
                                </div>
                                <span class="text-[10px] text-gray-400 font-black uppercase truncate max-w-[120px]">
                                    {{ $report->user->name ?? 'System' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="px-8 py-6 flex justify-between items-center bg-gray-50/50 group-hover:bg-indigo-50/20 transition-colors">
                        <a href="{{ route('examination-reports.show', $report) }}"
                            class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-900 text-xs font-black uppercase tracking-widest transition-all hover:translate-x-1">
                            <i class="fas fa-arrow-right-long animate-bounce-x"></i> Detail Laporan
                        </a>

                        <form action="{{ route('examination-reports.destroy', $report) }}" method="POST"
                            onsubmit="confirmDelete(event)">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-8 h-8 rounded-xl bg-white border border-gray-200 text-gray-300 hover:bg-red-50 hover:text-red-500 hover:border-red-100 transition-all flex items-center justify-center shadow-sm">
                                <i class="fas fa-trash-can text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full py-20 px-6 bg-white rounded-[3rem] border border-gray-100 shadow-xl shadow-gray-100/50 text-center">
                    <div class="relative w-24 h-24 mx-auto mb-8">
                        <div class="absolute inset-0 bg-indigo-50 rounded-full animate-ping opacity-25"></div>
                        <div
                            class="relative w-full h-full bg-white rounded-full border-4 border-indigo-50 flex items-center justify-center overflow-hidden">
                            <i
                                class="fas fa-folder-open text-indigo-100 text-7xl absolute translate-x-3 translate-y-3 opacity-20"></i>
                            <i class="fas fa-file-circle-plus text-indigo-400 text-4xl"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 uppercase">Belum ada laporan pemeriksaan</h3>
                    <p class="mt-2 text-sm text-gray-400 font-medium max-w-md mx-auto">Rapikan arsip laporan pemeriksaan
                        Anda dengan mulai mengunggah file atau foto hasil temuan di lapangan.</p>
                    <a href="{{ route('examination-reports.create') }}"
                        class="mt-8 inline-block bg-indigo-600 text-white font-black px-10 py-4 rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all uppercase tracking-widest text-xs">Ayo
                        Mulai Sekarang</a>
                </div>
            @endforelse
        </div>
    </div>

    <style>
        @keyframes bounce-x {

            0%,
            100% {
                transform: translateX(0);
            }

            50% {
                transform: translateX(3px);
            }
        }

        .animate-bounce-x {
            animation: bounce-x 1s infinite;
        }
    </style>

    <script>
        function confirmDelete(event) {
            event.preventDefault();
            const form = event.target;
            Swal.fire({
                title: 'Hapus Laporan?',
                text: "Dokumen ini akan dihapus permanen dari arsip sistem.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4F46E5', // Indigo-600
                cancelButtonColor: '#9CA3AF', // Gray-400
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    container: 'font-serif',
                    popup: 'rounded-[2rem] border-none shadow-2xl',
                    confirmButton: 'rounded-xl font-black uppercase tracking-widest',
                    cancelButton: 'rounded-xl font-black uppercase tracking-widest'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        }
    </script>
@endsection
