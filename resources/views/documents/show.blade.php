@extends('layouts.app')

@section('title', $document->title)

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 py-8 h-[calc(100vh-6rem)] flex flex-col">
        <div class="flex items-center justify-between mb-4 flex-shrink-0">
            <div>
                <h1 class="text-xl font-black text-gray-900 uppercase tracking-tight">{{ $document->title }}</h1>
                <p class="text-xs text-gray-500">{{ $document->description }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ asset('storage/' . $document->file_path) }}" download
                    class="rounded-lg bg-gray-100 px-3 py-2 text-sm font-bold text-gray-700 hover:bg-gray-200">
                    <i class="fas fa-download mr-1"></i> Download
                </a>
                <a href="{{ route('documents.index') }}"
                    class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-bold text-white hover:bg-indigo-500">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="flex-1 bg-gray-100 rounded-2xl overflow-hidden border border-gray-200 shadow-inner relative">
            <iframe src="{{ asset('storage/' . $document->file_path) }}" class="w-full h-full" frameborder="0">
                <p>Browser Anda tidak mendukung preview PDF. Silakan <a
                        href="{{ asset('storage/' . $document->file_path) }}">Download File</a>.</p>
            </iframe>
        </div>
    </div>
@endsection
