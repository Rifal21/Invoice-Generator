@extends('layouts.app')

@section('title', 'Backup & Google Drive')

@section('content')
    <div class="px-4 py-8">
        <div class="sm:flex sm:items-center mb-8">
            <div class="sm:flex-auto">
                <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tight">Cloud Backup</h1>
                <p class="mt-2 text-sm text-gray-700">Fitur ini mengarsipkan Invoice & Laba Rugi ke dalam format PDF yang
                    terstruktur untuk di-upload ke Google Drive.</p>
            </div>
        </div>

        <div class="max-w-xl mx-auto">
            <!-- Backup Form Card -->
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden mb-8">
                <div class="p-8">
                    <div class="mb-8">
                        <p class="text-center text-sm text-gray-500 mb-4">Pilih jenis backup yang ingin di-upload ke Google
                            Drive.</p>
                        <div x-data="{ type: 'monthly' }">
                            <form id="backup-form" action="{{ route('backup.process') }}" method="POST">
                                @csrf
                                <div class="mb-6">
                                    <label class="block text-xs font-bold text-gray-700 mb-2 uppercase">Jenis Backup</label>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                        <button type="button" @click="type = 'monthly'"
                                            :class="type === 'monthly' ? 'bg-indigo-600 text-white' :
                                                'bg-gray-100 text-gray-600'"
                                            class="py-2 px-2 rounded-xl text-[10px] font-black transition-all">
                                            BULANAN
                                        </button>
                                        <button type="button" @click="type = 'weekly'"
                                            :class="type === 'weekly' ? 'bg-indigo-600 text-white' :
                                                'bg-gray-100 text-gray-600'"
                                            class="py-2 px-2 rounded-xl text-[10px] font-black transition-all">
                                            MINGGUAN
                                        </button>
                                        <button type="button" @click="type = 'custom'"
                                            :class="type === 'custom' ? 'bg-indigo-600 text-white' :
                                                'bg-gray-100 text-gray-600'"
                                            class="py-2 px-2 rounded-xl text-[10px] font-black transition-all">
                                            CUSTOM / PELANGGAN
                                        </button>
                                        <button type="button" @click="type = 'database'"
                                            :class="type === 'database' ? 'bg-indigo-600 text-white' :
                                                'bg-gray-100 text-gray-600'"
                                            class="py-2 px-2 rounded-xl text-[10px] font-black transition-all">
                                            DATABASE
                                        </button>
                                    </div>
                                    <input type="hidden" name="type" :value="type">
                                </div>

                                <div class="mb-6">
                                    <!-- Database Info -->
                                    <div x-show="type === 'database'"
                                        class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-4"
                                        style="display: none;">
                                        <p class="text-xs text-blue-800 leading-relaxed font-medium">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Backup ini akan mengupload <strong>seluruh data website</strong> ke Google Drive
                                            dalam dua format:
                                            <span class="block mt-1 font-bold">• SQL (Untuk pemulihan sistem)</span>
                                            <span class="block font-bold">• Excel (Dapat dibaca langsung di
                                                spreadsheet)</span>
                                        </p>
                                    </div>

                                    <!-- Monthly Inputs -->
                                    <div x-show="type === 'monthly'" class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="month"
                                                class="block text-xs font-bold text-gray-700 mb-2 uppercase">Bulan</label>
                                            <select id="month" name="month"
                                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 px-4 bg-gray-50 font-bold">
                                                @foreach (range(1, 12) as $m)
                                                    <option value="{{ $m }}"
                                                        {{ $m == date('n') ? 'selected' : '' }}>
                                                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label for="year"
                                                class="block text-xs font-bold text-gray-700 mb-2 uppercase">Tahun</label>
                                            <select id="year" name="year"
                                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 px-4 bg-gray-50 font-bold">
                                                @foreach (range(date('Y'), date('Y') - 5) as $y)
                                                    <option value="{{ $y }}"
                                                        {{ $y == date('Y') ? 'selected' : '' }}>
                                                        {{ $y }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Weekly / Custom Range Inputs -->
                                    <div x-show="type === 'weekly' || type === 'custom'" class="grid grid-cols-2 gap-4"
                                        style="display: none;">
                                        <div>
                                            <label for="start_date"
                                                class="block text-xs font-bold text-gray-700 mb-2 uppercase">Tanggal
                                                Mulai</label>
                                            <input type="date" id="start_date" name="start_date"
                                                value="{{ date('Y-m-d', strtotime('last Monday')) }}"
                                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 px-4 bg-gray-50 font-bold">
                                        </div>
                                        <div>
                                            <label for="end_date"
                                                class="block text-xs font-bold text-gray-700 mb-2 uppercase">Tanggal
                                                Selesai</label>
                                            <input type="date" id="end_date" name="end_date" value="{{ date('Y-m-d') }}"
                                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 px-4 bg-gray-50 font-bold">
                                        </div>
                                    </div>

                                    <!-- Customer Select (Only for Custom) -->
                                    <div x-show="type === 'custom'" class="mt-4" style="display: none;">
                                        <label for="customer_id"
                                            class="block text-xs font-bold text-gray-700 mb-2 uppercase">Filter Pelanggan
                                            (Opsional)</label>
                                        <select id="customer_id" name="customer_id" class="select2 block w-full">
                                            <option value="">-- Semua Pelanggan --</option>
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                        <p class="text-[10px] text-gray-400 mt-1">Biarkan kosong untuk membackup semua
                                            pelanggan dalam rentang tanggal.</p>
                                    </div>
                                </div>

                                <!-- Status Google Drive -->
                                <div
                                    class="mb-6 p-4 rounded-xl {{ $isConnected ? 'bg-green-50 border border-green-100' : 'bg-red-50 border border-red-100' }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="p-2 rounded-full {{ $isConnected ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                                <i class="fab fa-google-drive text-xl"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-800">Status Koneksi</h4>
                                                <p class="text-xs {{ $isConnected ? 'text-green-700' : 'text-red-700' }}">
                                                    {{ $isConnected ? 'Terhubung dengan Akun Google.' : 'Belum terhubung / Token Kadaluarsa.' }}
                                                </p>
                                            </div>
                                        </div>
                                        @if (!$isConnected)
                                            <a href="{{ route('backup.connect') }}"
                                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition-colors">
                                                HUBUNGKAN AKUN
                                            </a>
                                        @else
                                            <span
                                                class="px-3 py-1 bg-green-200 text-green-800 text-xs font-bold rounded-lg">
                                                CONNECTED
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Progress Indicator -->
                                <div id="progress-container"
                                    class="hidden mb-6 bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <div class="flex justify-between mb-2">
                                        <span class="text-xs font-bold text-indigo-700" id="progress-text">Proses
                                            Backup...</span>
                                        <span class="text-xs font-bold text-indigo-700" id="progress-percentage">0%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-300"
                                            style="width: 0%" id="progress-bar"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2 font-mono" id="progress-message">Menyiapkan...
                                    </p>
                                </div>

                                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6">
                                    <h4 class="text-sm font-bold text-blue-900 mb-2"><i
                                            class="fas fa-info-circle mr-1"></i>
                                        Cara
                                        Kerja:</h4>
                                    <ul class="text-xs text-blue-800 space-y-1 list-disc list-inside">
                                        <li>Pilih Bulan & Tahun.</li>
                                        <li><strong>Sistem akan upload langsung ke Google Drive.</strong></li>
                                        <li>Pastikan file <code>credentials.json</code> sudah terpasang dan Status
                                            Connected.</li>
                                        <li>Proses berjalan di background. JANGAN TUTUP halaman ini sampai selesai.</li>
                                    </ul>
                                </div>

                                <button type="submit" id="btn-submit"
                                    class="w-full flex justify-center items-center gap-2 py-4 px-4 border border-transparent rounded-2xl shadow-sm text-sm font-black text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all hover:scale-[1.02]">
                                    <i id="btn-spinner" class="fas fa-spinner fa-spin hidden"></i>
                                    <i id="btn-icon" class="fab fa-google-drive text-lg"></i>
                                    <span id="btn-text">MULAI UPLOAD KE GOOGLE DRIVE</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Manual DB Backup Card -->
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden mb-8">
                        <div class="p-8">
                            <div class="flex items-center justify-center mb-6 text-indigo-500">
                                <i class="fas fa-database text-6xl"></i>
                            </div>
                            <h2 class="text-center text-xl font-bold text-gray-900 mb-2">Manual Database Backup</h2>
                            <p class="text-center text-sm text-gray-500 mb-8">Download keseluruhan database website (format
                                .sql)
                                secara langsung ke komputer Anda.</p>

                            <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 mb-6">
                                <h4 class="text-sm font-bold text-amber-900 mb-2"><i
                                        class="fas fa-exclamation-triangle mr-1"></i>
                                    Penting:</h4>
                                <p class="text-xs text-amber-800">File ini berisi seluruh data transaksi, produk, dan
                                    pengguna.
                                    Simpan di tempat yang aman.</p>
                            </div>

                            <a href="{{ route('backup.database') }}"
                                class="w-full flex justify-center items-center gap-2 py-4 px-4 border border-transparent rounded-2xl shadow-sm text-sm font-black text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all hover:scale-[1.02]">
                                <i class="fas fa-download text-lg"></i>
                                DOWNLOAD DATABASE (.SQL)
                            </a>
                        </div>
                    </div>

                    <!-- History List -->
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="text-lg font-bold text-gray-900">Riwayat Backup</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Periode</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Ket</th>
                                    </tr>
                                </thead>
                                <tbody id="backup-history-body" class="bg-white divide-y divide-gray-200">
                                    @include('backup.history', ['backups' => $backups ?? []])
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            <!-- SweetAlert2 -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <script>
                (function() {
                    const initBackupPage = function() {
                        // Force Reset Button State
                        const btnSubmit = document.getElementById('btn-submit');
                        if (btnSubmit) {
                            btnSubmit.disabled = false;
                            btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
                            const s = document.getElementById('btn-spinner');
                            if (s) s.classList.add('hidden');
                            const i = document.getElementById('btn-icon');
                            if (i) i.classList.remove('hidden');
                            const t = document.getElementById('btn-text');
                            if (t) t.innerText = 'MULAI UPLOAD KE GOOGLE DRIVE';
                        }

                        const formObj = document.getElementById('backup-form');
                        if (!formObj) return;

                        // Remove existing listener to avoid duplicates if any (though body replacement usually clears them)
                        const newForm = formObj.cloneNode(true);
                        formObj.parentNode.replaceChild(newForm, formObj);

                        // Re-select after clone
                        const activeForm = document.getElementById('backup-form');

                        // Initialize Select2
                        if ($('.select2').length > 0) {
                            // Destroy previous instance if any to prevent memory leaks/duplication
                            if ($('.select2').data('select2')) {
                                $('.select2').select2('destroy');
                            }
                            $('.select2').select2({
                                width: '100%',
                                placeholder: "-- Pilih Pelanggan (Optional) --",
                                allowClear: true
                            });
                        }

                        function updateHistory() {
                            fetch("{{ route('backup.history') }}")
                                .then(res => res.text())
                                .then(html => {
                                    const body = document.getElementById('backup-history-body');
                                    if (body) body.innerHTML = html;
                                })
                                .catch(e => console.error(e));
                        }

                        // Attach Submit Listener
                        activeForm.addEventListener('submit', function(e) {
                            e.preventDefault();

                            const form = this;
                            const btn = document.getElementById('btn-submit');
                            const container = document.getElementById('progress-container');
                            const pBar = document.getElementById('progress-bar');
                            const pText = document.getElementById('progress-text');
                            const pPerc = document.getElementById('progress-percentage');
                            const pMsg = document.getElementById('progress-message');

                            // Reset UI
                            container.classList.remove('hidden');
                            btn.disabled = true;
                            btn.classList.add('opacity-50', 'cursor-not-allowed');
                            document.getElementById('btn-spinner').classList.remove('hidden');
                            document.getElementById('btn-icon').classList.add('hidden');
                            document.getElementById('btn-text').innerText = 'MEMPROSES...';
                            pBar.style.width = '0%';
                            pPerc.innerText = '0%';
                            pMsg.innerText = 'Memulai proses...';

                            const formData = new FormData(form);

                            // Start Process
                            fetch(form.action, {
                                    method: 'POST',
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                                    },
                                    body: formData
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'success') {
                                        localStorage.setItem('backup_active', 'true');
                                        window.dispatchEvent(new Event('storage'));

                                        updateHistory();
                                        startPolling();
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal Memulai',
                                            text: data.message || 'Unknown error'
                                        });
                                        resetBtn();
                                    }
                                })
                                .catch(err => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error System',
                                        text: 'Gagal mengirim form: ' + err
                                    });
                                    resetBtn();
                                });

                            function startPolling() {
                                const interval = setInterval(() => {
                                    updateHistory();

                                    fetch("{{ route('backup.progress') }}")
                                        .then(res => res.json())
                                        .then(progress => {
                                            if (!document.getElementById('progress-bar')) {
                                                clearInterval(interval);
                                                return; // Page changed
                                            }

                                            pBar.style.width = progress.percentage + '%';
                                            pPerc.innerText = progress.percentage + '%';
                                            pMsg.innerText = progress.message;

                                            if (progress.status === 'completed') {
                                                clearInterval(interval);
                                                updateHistory();
                                                pMsg.innerText = 'SELESAI! ' + progress.message;
                                                pBar.classList.replace('bg-indigo-600', 'bg-green-500');

                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Backup Selesai!',
                                                    text: progress.message,
                                                    confirmButtonColor: '#4F46E5',
                                                    confirmButtonText: 'Mantap!'
                                                });
                                                resetBtn();
                                            } else if (progress.status === 'error') {
                                                clearInterval(interval);
                                                updateHistory();
                                                pMsg.innerText = 'ERROR: ' + progress.message;
                                                pBar.classList.replace('bg-indigo-600', 'bg-red-500');

                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Terjadi Kesalahan',
                                                    text: progress.message
                                                });
                                                resetBtn();
                                            }
                                        })
                                        .catch(err => {
                                            console.error('Polling error', err);
                                            clearInterval(interval);
                                        });
                                }, 2000);
                            }

                            function resetBtn() {
                                if (!btn) return;
                                btn.disabled = false;
                                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                                document.getElementById('btn-spinner').classList.add('hidden');
                                document.getElementById('btn-icon').classList.remove('hidden');
                                document.getElementById('btn-text').innerText = 'MULAI UPLOAD KE GOOGLE DRIVE';
                            }
                        });
                    };

                    // Run on load and after Turbo navigation
                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', initBackupPage);
                    } else {
                        initBackupPage();
                    }

                    // Specific Turbo listener for re-visits if body script re-execution isn't enough (it usually is for inline scripts, but safety first)
                    // document.addEventListener('turbo:load', initBackupPage); 
                    // Note: If the script is in the body, Turbo executes it. If we add a listener here, it might run twice. 
                    // Ideally, the IIFE + readyState check is sufficient for body scripts.

                })();
            </script>

            <style>
                /* Custom Select2 Styling to match Tailwind 'rounded-xl' and 'py-3' */
                .select2-container .select2-selection--single {
                    height: 50px !important;
                    /* Match py-3 input height */
                    border-radius: 0.75rem !important;
                    /* rounded-xl */
                    border-color: #d1d5db !important;
                    /* gray-300 */
                    display: flex !important;
                    align-items: center !important;
                    background-color: #f9fafb !important;
                    /* gray-50 */
                }

                .select2-container--default .select2-selection--single .select2-selection__rendered {
                    line-height: normal !important;
                    padding-left: 1rem !important;
                    /* pl-4 */
                    color: #111827 !important;
                    /* gray-900 */
                    font-weight: 700 !important;
                    /* font-bold */
                    font-size: 0.875rem !important;
                    /* text-sm */
                    width: 100%;
                }

                .select2-container--default .select2-selection--single .select2-selection__arrow {
                    height: 48px !important;
                    right: 10px !important;
                }

                .select2-dropdown {
                    border-radius: 0.75rem !important;
                    border-color: #d1d5db !important;
                    overflow: hidden;
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
                }
            </style>
        @endsection
