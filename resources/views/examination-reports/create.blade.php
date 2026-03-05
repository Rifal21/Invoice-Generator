@extends('layouts.app')

@section('title', 'Upload Laporan Pemeriksaan')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 py-8" x-data="{
        previewType: null,
        fileName: '',
        previewUrl: null,
        showCamera: false,
        updateFileName(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                this.fileName = file.name;
                if (file.type === 'application/pdf') {
                    this.previewType = 'pdf';
                    this.previewUrl = null;
                } else if (file.type.startsWith('image/')) {
                    this.previewType = 'image';
                    this.previewUrl = URL.createObjectURL(file);
                } else {
                    this.previewType = 'file';
                    this.previewUrl = null;
                }
            } else {
                this.fileName = '';
                this.previewType = null;
                this.previewUrl = null;
            }
        },
        async startCamera() {
            try {
                this.showCamera = true;
                const constraints = {
                    video: {
                        facingMode: {
                            ideal: 'environment'
                        },
                        width: {
                            ideal: 1920
                        },
                        height: {
                            ideal: 1080
                        }
                    }
                };
                const stream = await navigator.mediaDevices.getUserMedia(constraints);
                this.$refs.cameraFeed.srcObject = stream;
            } catch (err) {
                console.error('Error accessing camera:', err);
                alert('Tidak dapat mengakses kamera belakang. Pastikan izin kamera diberikan.');
                this.showCamera = false;
            }
        },
        stopCamera() {
            const stream = this.$refs.cameraFeed.srcObject;
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
            this.$refs.cameraFeed.srcObject = null;
            this.showCamera = false;
        },
        capturePhoto() {
            const canvas = document.createElement('canvas');
            const video = this.$refs.cameraFeed;
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
    
            canvas.toBlob((blob) => {
                const file = new File([blob], 'camera_capture.jpg', { type: 'image/jpeg' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                this.$refs.mainFileInput.files = dataTransfer.files;
                this.updateFileName(this.$refs.mainFileInput);
                this.stopCamera();
            }, 'image/jpeg');
        },
        triggerFile() {
            this.$refs.mainFileInput.click();
        }
    }">
        <div class="max-w-2xl mx-auto">
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="min-w-0 flex-1">
                    <h2
                        class="text-2xl font-black leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight uppercase">
                        Buat Laporan Baru
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 font-medium">Lengkapi detail laporan pemeriksaan tanpa judul.</p>
                </div>
                <div class="mt-4 flex md:ml-4 md:mt-0">
                    <a href="{{ route('examination-reports.index') }}"
                        class="inline-flex items-center rounded-xl bg-white px-3 py-2 text-sm font-bold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-all">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="bg-white shadow-2xl rounded-[2.5rem] overflow-hidden border border-gray-100">
                <form action="{{ route('examination-reports.store') }}" method="POST" enctype="multipart/form-data"
                    class="p-8 md:p-10 space-y-8">
                    @csrf

                    <!-- Hidden Single File Input -->
                    <input type="file" name="file" x-ref="mainFileInput" class="hidden"
                        accept="application/pdf,image/*" @change="updateFileName($event.target)">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Customer Selection -->
                        <div class="md:col-span-2">
                            <label for="customer_id"
                                class="block text-sm font-black text-gray-700 uppercase tracking-widest mb-2">Pilih
                                Pelanggan <span class="text-red-500">*</span></label>
                            <select name="customer_id" id="customer_id" required
                                class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-4 text-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold appearance-none">
                                <option value="">-- Cari Pelanggan --</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Report Date -->
                        <div class="md:col-span-2">
                            <label for="report_date"
                                class="block text-sm font-black text-gray-700 uppercase tracking-widest mb-2">Tanggal
                                Pemeriksaan <span class="text-red-500">*</span></label>
                            <input type="date" name="report_date" id="report_date" required value="{{ date('Y-m-d') }}"
                                class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-4 text-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold">
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description"
                            class="block text-sm font-black text-gray-700 uppercase tracking-widest mb-2">Keterangan /
                            Temuan (Opsional)</label>
                        <textarea id="description" name="description" rows="4"
                            class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-4 text-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium"
                            placeholder="Tuliskan catatan detail hasil pemeriksaan di sini..."></textarea>
                    </div>

                    <!-- File Upload Option -->
                    <div class="space-y-4">
                        <label class="block text-sm font-black text-gray-700 uppercase tracking-widest">Unggah Berkas
                            Laporan
                            <span class="text-red-500">*</span></label>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Option 1: Capture Photo -->
                            <button type="button" @click="startCamera()" class="group" x-show="!showCamera">
                                <div
                                    class="bg-emerald-50 border-2 border-emerald-100 group-hover:border-emerald-500 rounded-3xl p-6 text-center transition-all group-hover:scale-105 active:scale-95">
                                    <div
                                        class="w-12 h-12 bg-emerald-500 text-white rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg group-hover:rotate-12 transition-transform">
                                        <i class="fas fa-camera text-xl"></i>
                                    </div>
                                    <span class="block text-emerald-700 font-black text-sm uppercase">Ambil Foto</span>
                                    <span class="text-[10px] text-emerald-600 font-medium">Kamera Langsung</span>
                                </div>
                            </button>

                            <!-- Option 2: Upload File -->
                            <button type="button" @click="triggerFile()" class="group" x-show="!showCamera">
                                <div
                                    class="bg-indigo-50 border-2 border-indigo-100 group-hover:border-indigo-500 rounded-3xl p-6 text-center transition-all group-hover:scale-105 active:scale-95">
                                    <div
                                        class="w-12 h-12 bg-indigo-500 text-white rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg group-hover:-rotate-12 transition-transform">
                                        <i class="fas fa-file-upload text-xl"></i>
                                    </div>
                                    <span class="block text-indigo-700 font-black text-sm uppercase">Pilih File</span>
                                    <span class="text-[10px] text-indigo-600 font-medium">PDF/Gambar</span>
                                </div>
                            </button>
                        </div>

                        <!-- Camera Preview Container -->
                        <div x-show="showCamera" x-transition
                            class="relative bg-black rounded-[2rem] overflow-hidden aspect-video shadow-2xl border-4 border-white">
                            <video x-ref="cameraFeed" class="w-full h-full object-cover" autoplay playsinline></video>
                            <div class="absolute bottom-6 left-0 right-0 flex justify-center gap-6">
                                <button type="button" @click="capturePhoto()"
                                    class="bg-white rounded-full p-5 shadow-2xl hover:scale-110 active:scale-90 transition-transform border-4 border-gray-100">
                                    <div class="w-6 h-6 rounded-full bg-red-600 animate-pulse"></div>
                                </button>
                                <button type="button" @click="stopCamera()"
                                    class="bg-gray-900/80 text-white rounded-2xl px-6 py-2 text-xs font-black uppercase tracking-widest backdrop-blur-md hover:bg-red-600 transition-colors">
                                    Batal
                                </button>
                            </div>
                        </div>

                        <!-- Selected File Preview & Status -->
                        <div x-show="fileName && !showCamera" x-transition
                            class="bg-gray-50 border border-gray-100 rounded-[2rem] p-6 space-y-4">

                            <!-- Image Preview if available -->
                            <template x-if="previewUrl">
                                <div
                                    class="relative w-full aspect-video bg-gray-200 rounded-2xl overflow-hidden shadow-inner border-2 border-white">
                                    <img :src="previewUrl" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                                </div>
                            </template>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-sm"
                                        :class="previewType === 'pdf' ? 'bg-red-500 text-white' : (
                                            previewType === 'image' ? 'bg-emerald-500 text-white' :
                                            'bg-indigo-500 text-white')">
                                        <i class="fas"
                                            :class="previewType === 'pdf' ? 'fa-file-pdf text-xl' : (
                                                previewType === 'image' ?
                                                'fa-image text-xl' : 'fa-file text-xl')"></i>
                                    </div>
                                    <div class="max-w-[250px]">
                                        <p class="text-xs font-black text-gray-900 truncate" x-text="fileName"></p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest"
                                            x-text="previewType + ' Terpilih'"></p>
                                    </div>
                                </div>
                                <button type="button"
                                    @click="fileName = ''; previewType = null; previewUrl = null; $refs.mainFileInput.value = ''"
                                    class="w-10 h-10 rounded-full bg-white text-gray-300 hover:text-red-500 hover:bg-red-50 transition-all flex items-center justify-center shadow-sm border border-gray-100">
                                    <i class="fas fa-trash-can text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="pt-6 border-t border-gray-100">
                        <button type="submit" x-show="!showCamera"
                            class="w-full rounded-2xl bg-indigo-600 px-10 py-5 text-base font-black text-white shadow-xl shadow-indigo-100 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-100 transition-all hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3 uppercase tracking-widest">
                            <i class="fas fa-save text-xl"></i> Simpan Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
