@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-10">
        <!-- Centered Layout -->
        <div class="max-w-3xl mx-auto">
            <div class="mb-8">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('products.index') }}"
                                class="text-xs md:text-sm font-bold text-gray-400 hover:text-indigo-600">Produk</a></li>
                        <li><svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg></li>
                        <li class="text-xs md:text-sm font-bold text-indigo-600">Buat Baru</li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Tambah Produk</h1>
                <p class="mt-2 text-sm text-gray-500">Lengkapi detail produk atau layanan baru Anda.</p>
            </div>

            <div class="bg-white shadow-xl rounded-3xl border border-gray-100 overflow-hidden">
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data"
                    class="p-6 md:p-10">
                    @csrf

                    <div class="space-y-8">
                        <!-- Image Upload Section -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-2 mb-6">Foto Produk</h3>
                            <div class="flex flex-col sm:flex-row gap-6 items-start">
                                <!-- Preview -->
                                <div
                                    class="relative w-40 h-40 bg-gray-100 rounded-2xl overflow-hidden border-2 border-dashed border-gray-300 flex items-center justify-center group">
                                    <img id="image-preview" src="#" alt="Preview"
                                        class="w-full h-full object-cover hidden">
                                    <div id="placeholder-icon" class="text-gray-400 flex flex-col items-center">
                                        <svg class="h-10 w-10 mb-1" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-xs font-bold">No Image</span>
                                    </div>
                                </div>

                                <!-- Inputs -->
                                <div class="flex-1 space-y-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-900 mb-2">Upload Foto</label>
                                        <input type="file" name="image" id="image-upload" accept="image/*"
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all">
                                    </div>

                                    <div class="relative">
                                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                            <div class="w-full border-t border-gray-200"></div>
                                        </div>
                                        <div class="relative flex justify-center">
                                            <span
                                                class="bg-white px-2 text-xs font-bold text-gray-400 uppercase tracking-widest">Atau</span>
                                        </div>
                                    </div>

                                    <button type="button" onclick="startCamera()"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border-2 border-indigo-100 rounded-xl text-sm font-bold text-indigo-700 hover:bg-indigo-50 transition-all">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Ambil Foto Langsung
                                    </button>
                                </div>
                            </div>

                            <!-- Hidden Camera Elements -->
                            <div id="camera-container"
                                class="hidden mt-4 relative bg-black rounded-2xl overflow-hidden aspect-video">
                                <video id="camera-feed" class="w-full h-full object-cover" autoplay playsinline></video>
                                <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-4">
                                    <button type="button" onclick="capturePhoto()"
                                        class="bg-white rounded-full p-4 shadow-lg hover:scale-110 transition-transform">
                                        <div class="w-4 h-4 rounded-full bg-red-600"></div>
                                    </button>
                                    <button type="button" onclick="stopCamera()"
                                        class="bg-gray-800/80 text-white rounded-full px-4 py-2 text-xs font-bold backdrop-blur-sm">
                                        Batal
                                    </button>
                                </div>
                            </div>
                            <!-- Hidden input for base64 -->
                            <input type="hidden" name="image_base64" id="image-base64">
                        </div>

                        <!-- Basic Info Section -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-2 mb-6">Informasi Dasar
                            </h3>
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-bold text-gray-900 mb-2">Nama
                                        Produk</label>
                                    <input type="text" name="name" id="name" required
                                        placeholder="Contoh: Kopi Susu Aren"
                                        class="block w-full rounded-2xl border-gray-200 py-3 px-4 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                </div>

                                <div>
                                    <label for="category_id"
                                        class="block text-sm font-bold text-gray-900 mb-2">Kategori</label>
                                    <div class="relative">
                                        <select id="category_id" name="category_id" required
                                            class="block w-full rounded-2xl border-gray-200 py-3 px-4 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all appearance-none">
                                            <option value="">Pilih Kategori</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label for="supplier_id" class="block text-sm font-bold text-gray-900 mb-2">Supplier
                                        (Opsional)</label>
                                    <div class="relative">
                                        <select id="supplier_id" name="supplier_id"
                                            class="block w-full rounded-2xl border-gray-200 py-3 px-4 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all appearance-none">
                                            <option value="">Pilih Supplier</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing & Unit Section -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-2 mb-6">Harga & Satuan
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="price" class="block text-sm font-bold text-gray-900 mb-2">Harga
                                        Jual</label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                            <span class="text-gray-500 font-bold">Rp</span>
                                        </div>
                                        <input type="number" name="price" id="price" step="0.01" required
                                            placeholder="0"
                                            class="block w-full rounded-2xl border-gray-200 py-3 pl-12 pr-4 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold">
                                    </div>
                                </div>

                                <div>
                                    <label for="purchase_price" class="block text-sm font-bold text-gray-900 mb-2">Harga
                                        Beli (HPP)</label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                            <span class="text-gray-500 font-bold">Rp</span>
                                        </div>
                                        <input type="number" name="purchase_price" id="purchase_price" step="0.01"
                                            placeholder="0"
                                            class="block w-full rounded-2xl border-gray-200 py-3 pl-12 pr-4 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold">
                                    </div>
                                </div>

                                <div>
                                    <label for="unit"
                                        class="block text-sm font-bold text-gray-900 mb-2">Satuan</label>
                                    <input type="text" name="unit" id="unit" required
                                        placeholder="Contoh: Pcs, Kg, Box"
                                        class="block w-full rounded-2xl border-gray-200 py-3 px-4 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                </div>

                                <div>
                                    <label for="stock" class="block text-sm font-bold text-gray-900 mb-2">Stok
                                        Awal</label>
                                    <input type="number" name="stock" id="stock" step="0.01" value="0"
                                        required
                                        class="block w-full rounded-2xl border-gray-200 py-3 px-4 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                </div>
                            </div>
                        </div>

                        <!-- Description Section -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-2 mb-6">Keterangan
                                Tambahan</h3>
                            <label for="description" class="block text-sm font-bold text-gray-900 mb-2">Deskripsi
                                (Opsional)</label>
                            <textarea id="description" name="description" rows="4"
                                placeholder="Tulis deskripsi singkat tentang produk ini..."
                                class="block w-full rounded-2xl border-gray-200 py-3 px-4 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all"></textarea>
                        </div>
                    </div>

                    <div class="mt-10 pt-6 border-t border-gray-100 flex items-center justify-end gap-4">
                        <a href="{{ route('products.index') }}"
                            class="px-6 py-3 rounded-2xl text-sm font-bold text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-all">
                            Batal
                        </a>
                        <button type="submit"
                            class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-sm font-bold rounded-2xl text-white bg-indigo-600 shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all hover:-translate-y-0.5">
                            Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    <script>
        const imageUpload = document.getElementById('image-upload');
        const imagePreview = document.getElementById('image-preview');
        const placeholderIcon = document.getElementById('placeholder-icon');
        const cameraContainer = document.getElementById('camera-container');
        const cameraFeed = document.getElementById('camera-feed');
        const imageBase64 = document.getElementById('image-base64');
        let stream = null;

        // Handle File Upload Preview
        imageUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                    placeholderIcon.classList.add('hidden');
                    // Clear base64 if file is selected
                    imageBase64.value = '';
                }
                reader.readAsDataURL(file);
            }
        });

        // Camera Functions
        async function startCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'environment'
                    }
                });
                cameraFeed.srcObject = stream;
                cameraContainer.classList.remove('hidden');
            } catch (err) {
                console.error("Error accessing camera:", err);
                alert("Tidak dapat mengakses kamera via browser ini. Pastikan izin kamera diberikan.");
            }
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            cameraContainer.classList.add('hidden');
        }

        function capturePhoto() {
            const canvas = document.createElement('canvas');
            canvas.width = cameraFeed.videoWidth;
            canvas.height = cameraFeed.videoHeight;
            canvas.getContext('2d').drawImage(cameraFeed, 0, 0);

            const dataUrl = canvas.toDataURL('image/jpeg');

            // Set preview
            imagePreview.src = dataUrl;
            imagePreview.classList.remove('hidden');
            placeholderIcon.classList.add('hidden');

            // Set hidden input value (we'll use a hack to send this to server)
            // Since we can't set file input value programmatically for security,
            // we'll use a hidden input "image" if the controller supports base64 string detection
            // which we implemented in the Controller logic.
            // But wait, the controller checks $request->image. 
            // In a multipart form, 'image' input is the file. We need to use a separate input name or 
            // use JS to put base64 into a text input named 'image' and disable the file input?
            // Better: use the hidden input 'image-base64' and handle it in controller, OR
            // Rename the hidden input to be 'image' BUT we have a file input named 'image'.
            // Simple solution: Rename hidden input to 'image' and disable file input temporarily? 
            // No, easy way: The controller logic I wrote checks `request->image`. 
            // If I send `image` as text (base64), it works. But file input `image` will key conflict.
            // Let's change the controller to check `request->input('image_base64')` OR `request->file('image')`.
            // OR simpler: JS DataTransfer to create a file object from base64 and assign to file input.

            fetch(dataUrl)
                .then(res => res.blob())
                .then(blob => {
                    const file = new File([blob], "camera_capture.jpg", {
                        type: "image/jpeg"
                    });
                    const container = new DataTransfer();
                    container.items.add(file);
                    imageUpload.files = container.files;

                    // Clear base64 hidden input just in case
                    imageBase64.value = '';
                });

            stopCamera();
        }
    </script>
@endsection
