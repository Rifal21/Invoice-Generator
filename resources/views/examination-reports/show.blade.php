@extends('layouts.app')

@section('title', 'Detail Laporan Pemeriksaan')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 py-8 h-[calc(100vh-6rem)] flex flex-col" x-data="{ fullScreen: false }">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8 flex-shrink-0">
            <div class="flex items-center gap-4">
                <div
                    class="w-16 h-16 bg-white border-2 border-indigo-100 rounded-3xl flex items-center justify-center text-indigo-500 shadow-xl shadow-indigo-50 group transition-all duration-500 hover:rotate-6">
                    @php
                        $extension = strtolower(pathinfo($examinationReport->file_path, PATHINFO_EXTENSION));
                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                    @endphp
                    <i class="fas {{ $isImage ? 'fa-image' : 'fa-file-pdf' }} text-3xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span
                            class="px-3 py-1 bg-indigo-600 text-[10px] font-black uppercase text-white rounded-full shadow-lg shadow-indigo-100">
                            {{ $examinationReport->customer->name ?? 'UMUM' }}
                        </span>
                        <div
                            class="flex items-center gap-2 text-xs text-gray-400 font-black uppercase tracking-widest shrink-0">
                            <i class="far fa-calendar-alt text-indigo-400"></i>
                            {{ \Carbon\Carbon::parse($examinationReport->report_date)->format('d M Y') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 self-start md:self-center">
                <a href="{{ route('examination-reports.stream', $examinationReport) }}" download
                    class="inline-flex items-center gap-3 rounded-2xl bg-white border-2 border-gray-100 px-6 py-4 text-xs font-black text-gray-700 shadow-xl shadow-gray-100/50 hover:bg-gray-50 transition-all hover:scale-105 uppercase tracking-widest">
                    <i class="fas fa-cloud-arrow-down text-indigo-500 text-base"></i> Simpan File
                </a>
                <a href="{{ route('examination-reports.index') }}"
                    class="inline-flex items-center gap-3 rounded-2xl bg-indigo-600 px-6 py-4 text-xs font-black text-white shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all hover:scale-105 uppercase tracking-widest leading-none">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        @if ($examinationReport->description)
            <div
                class="mb-6 bg-white border border-gray-100 rounded-3xl p-6 shadow-sm flex-shrink-0 relative overflow-hidden group">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-indigo-50/30 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 group-hover:bg-indigo-100/30 transition-colors">
                </div>
                <h4 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-3 flex items-center gap-2">
                    <i class="fas fa-quote-left text-xs"></i> Catatan Pemeriksaan
                </h4>
                <p class="text-sm text-gray-600 font-medium leading-relaxed italic relative z-10">
                    "{{ $examinationReport->description }}"
                </p>
                <div class="mt-4 flex items-center gap-2 grayscale group-hover:grayscale-0 transition-all">
                    <div class="h-1 w-8 bg-indigo-200 rounded-full"></div>
                    <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">
                        Oleh: {{ $examinationReport->user->name ?? 'SYSTEM' }}
                    </span>
                </div>
            </div>
        @endif

        <div class="flex-1 bg-gray-50 rounded-[3rem] border border-gray-100 shadow-inner overflow-hidden relative group"
            :class="fullScreen ? 'fixed inset-4 z-[100] shadow-2xl backdrop-blur-3xl m-0' : 'relative p-6'">

            <button @click="fullScreen = !fullScreen"
                class="absolute top-8 right-8 z-10 w-12 h-12 bg-white/80 backdrop-blur shadow-xl border border-white/20 rounded-2xl flex items-center justify-center text-gray-400 hover:text-indigo-600 hover:scale-110 active:scale-90 transition-all opacity-0 group-hover:opacity-100">
                <i class="fas" :class="fullScreen ? 'fa-compress' : 'fa-expand'"></i>
            </button>

            <div class="w-full h-full flex items-center justify-center p-4">
                @if ($isImage)
                    <div class="relative w-full h-full flex items-center justify-center">
                        <img src="{{ route('examination-reports.stream', $examinationReport) }}" alt="Pratinjau Laporan"
                            class="max-w-full max-h-full object-contain rounded-2xl shadow-2xl border-4 border-white transition-all duration-700"
                            :class="fullScreen ? 'scale-100' : 'group-hover:scale-[1.01]'">

                        <!-- Watermark/Info when image -->
                        <div
                            class="absolute bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-3 px-6 py-3 bg-black/40 backdrop-blur-md rounded-2xl border border-white/10 text-white opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fas fa-camera text-indigo-400"></i>
                            <span class="text-[10px] font-black uppercase tracking-[0.15em] shrink-0">Captured @
                                {{ $examinationReport->created_at->format('H:i') }}</span>
                        </div>
                    </div>
                @else
                    <iframe
                        src="{{ route('examination-reports.stream', $examinationReport) }}#view=FitH&toolbar=0&navpanes=0"
                        class="w-full h-full rounded-2xl border-none bg-white shadow-2xl" frameborder="0">
                        <div class="text-center p-12">
                            <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-file-pdf text-4xl text-red-500 font-black"></i>
                            </div>
                            <p class="text-gray-900 font-black uppercase tracking-widest">Browser Tidak Mendukung Preview
                            </p>
                            <p class="text-gray-400 text-xs mt-2 font-medium">Dokumen PDF ini mungkin terlalu besar untuk
                                pratinjau langsung.</p>
                            <a href="{{ route('examination-reports.stream', $examinationReport) }}"
                                class="mt-8 inline-flex items-center gap-2 bg-indigo-600 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all">
                                <i class="fas fa-file-download"></i> Klik untuk Download
                            </a>
                        </div>
                    </iframe>
                @endif
            </div>
        </div>
    </div>
@endsection
