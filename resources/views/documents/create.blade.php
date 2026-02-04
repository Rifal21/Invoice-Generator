@extends('layouts.app')

@section('title', 'Upload Dokumen')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-black leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Upload Dokumen Baru
                    </h2>
                </div>
                <div class="mt-4 flex md:ml-4 md:mt-0">
                    <a href="{{ route('documents.index') }}"
                        class="inline-flex items-center rounded-xl bg-white px-3 py-2 text-sm font-bold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="bg-white shadow-xl rounded-3xl overflow-hidden border border-gray-100">
                <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data"
                    class="p-8 space-y-6">
                    @csrf

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-bold text-gray-700">Judul Dokumen <span
                                class="text-red-500">*</span></label>
                        <div class="mt-2">
                            <input type="text" name="title" id="title" required
                                class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                placeholder="Contoh: Akta Pendirian Koperasi">
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-bold text-gray-700">Deskripsi (Opsional)</label>
                        <div class="mt-2">
                            <textarea id="description" name="description" rows="3"
                                class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                placeholder="Keterangan singkat tentang dokumen..."></textarea>
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label for="file" class="block text-sm font-bold text-gray-700">File PDF <span
                                class="text-red-500">*</span></label>
                        <div
                            class="mt-2 flex justify-center rounded-xl border border-dashed border-gray-900/25 px-6 py-10 hover:bg-gray-50 transition-colors relative">
                            <div class="text-center">
                                <i class="fas fa-file-pdf text-4xl text-gray-300 mb-4" id="icon-preview"></i>
                                <div class="mt-4 flex text-sm leading-6 text-gray-600 justify-center">
                                    <label for="file"
                                        class="relative cursor-pointer rounded-md bg-white font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                                        <span>Upload a file</span>
                                        <input id="file" name="file" type="file" class="sr-only"
                                            accept="application/pdf" required onchange="updateFileName(this)">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs leading-5 text-gray-600">PDF up to 10MB</p>
                                <p id="file-name" class="text-sm font-bold text-indigo-600 mt-2 hidden"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="pt-4 border-t border-gray-100 flex justify-end">
                        <button type="submit"
                            class="rounded-xl bg-indigo-600 px-8 py-3 text-sm font-bold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all hover:scale-[1.02]">
                            <i class="fas fa-save mr-2"></i> Simpan Dokumen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateFileName(input) {
            const fileNameDisplay = document.getElementById('file-name');
            const iconPreview = document.getElementById('icon-preview');

            if (input.files && input.files[0]) {
                fileNameDisplay.textContent = input.files[0].name;
                fileNameDisplay.classList.remove('hidden');
                iconPreview.classList.add('text-red-500');
                iconPreview.classList.remove('text-gray-300');
            } else {
                fileNameDisplay.textContent = '';
                fileNameDisplay.classList.add('hidden');
                iconPreview.classList.remove('text-red-500');
                iconPreview.classList.add('text-gray-300');
            }
        }
    </script>
@endsection
