@extends('layouts.app')

@section('title', 'Dokumen Legalitas')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tight">Dokumen Legalitas</h1>
                <p class="mt-2 text-sm text-gray-700">Arsip dokumen legalitas dan file penting koperasi.</p>
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none flex items-center gap-3">
                <form id="backup-form" action="{{ route('documents.backup') }}" method="POST">
                    @csrf
                    <button type="button" onclick="confirmBackup()"
                        class="rounded-xl bg-white border border-gray-300 px-4 py-2 text-center text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 flex items-center gap-2 hover:scale-105 transition-all">
                        <i class="fab fa-google-drive text-green-500 text-lg"></i> Backup Cloud
                    </button>
                </form>

                <a href="{{ route('documents.create') }}"
                    class="block rounded-xl bg-indigo-600 px-4 py-2 text-center text-sm font-bold text-white shadow-sm hover:bg-indigo-500 hover:scale-105 transition-all">
                    <i class="fas fa-file-upload mr-2"></i> Upload Dokumen
                </a>
            </div>
        </div>

        <!-- Grid -->
        <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($documents as $document)
                <div
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow overflow-hidden flex flex-col">
                    <div class="p-6 flex-1">
                        <div class="flex items-start justify-between">
                            <div class="bg-red-50 text-red-600 rounded-lg p-3">
                                <i class="fas fa-file-pdf text-2xl"></i>
                            </div>
                            <div class="text-xs text-gray-400 font-mono">
                                {{ $document->created_at->format('d M Y') }}
                            </div>
                        </div>
                        <h3 class="mt-4 text-base font-bold text-gray-900">{{ $document->title }}</h3>
                        <p class="mt-1 text-sm text-gray-500 line-clamp-3">
                            {{ $document->description ?? 'Tidak ada deskripsi.' }}
                        </p>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-between items-center">
                        <a href="{{ route('documents.show', $document) }}"
                            class="text-indigo-600 hover:text-indigo-900 text-sm font-bold flex items-center gap-1">
                            <i class="fas fa-eye"></i> Lihat PDF
                        </a>

                        <form action="{{ route('documents.destroy', $document) }}" method="POST"
                            onsubmit="confirmDelete(event)">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-red-600 hover:text-red-900 text-sm font-bold flex items-center gap-1">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                    <i class="fas fa-folder-open text-gray-400 text-4xl mb-3"></i>
                    <p class="text-gray-500 font-medium">Belum ada dokumen yang diupload.</p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        function confirmBackup() {
            Swal.fire({
                title: 'Backup ke Drive?',
                text: "Semua dokumen akan diupload ke Google Drive. Proses berjalan di background.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Backup!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Trigger Global Indicator
                    localStorage.setItem('backup_active', 'true');
                    document.getElementById('backup-form').submit();
                }
            })
        }

        function confirmDelete(event) {
            event.preventDefault();
            const form = event.target;
            Swal.fire({
                title: 'Hapus Dokumen?',
                text: "File PDF juga akan dihapus dari server.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        }
    </script>
@endsection
