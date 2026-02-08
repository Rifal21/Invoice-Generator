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
                                    <div class="grid grid-cols-3 gap-2">
                                        <button type="button" @click="type = 'monthly'"
                                            :class="type === 'monthly' ? 'bg-indigo-600 text-white' :
                                                'bg-gray-100 text-gray-600'"
                                            class="py-2 px-2 rounded-xl text-[10px] font-black transition-all">
                                            BULANAN (PDF)
                                        </button>
                                        <button type="button" @click="type = 'weekly'"
                                            :class="type === 'weekly' ? 'bg-indigo-600 text-white' :
                                                'bg-gray-100 text-gray-600'"
                                            class="py-2 px-2 rounded-xl text-[10px] font-black transition-all">
                                            MINGGUAN (PDF)
                                        </button>
                                        <button type="button" @click="type = 'database'"
                                            :class="type === 'database' ? 'bg-indigo-600 text-white' :
                                                'bg-gray-100 text-gray-600'"
                                            class="py-2 px-2 rounded-xl text-[10px] font-black transition-all">
                                            DATABASE (SQL/EXCEL)
                                        </button>
                                    </div>
                                    <input type="hidden" name="type" :value="type">
                                </div>

                                <div class="mb-6">
                                    <!-- Database Info -->
                                    <div x-show="type === 'database'"
                                        class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-4">
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
                                    <div x-show="type === 'weekly'" class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="start_date"
                                                class="block text-xs font-bold text-gray-700 mb-2 uppercase">Tanggal
                                                Mulai</label>
                                            <input type="date" id="start_date" name="start_date"
                                                value="{{ date('Y-m-d', strtotime('last monday')) }}"
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
                                    <p class="text-xs text-gray-500 mt-2 font-mono" id="progress-message">Menyiapkan...</p>
                                </div>

                                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6">
                                    <h4 class="text-sm font-bold text-blue-900 mb-2"><i class="fas fa-info-circle mr-1"></i>
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
                document.addEventListener('DOMContentLoaded', function() {
                    const formObj = document.getElementById('backup-form');
                    if (!formObj) return;

                    function updateHistory() {
                        fetch("{{ route('backup.history') }}")
                            .then(res => res.text())
                            .then(html => {
                                document.getElementById('backup-history-body').innerHTML = html;
                            })
                            .catch(e => console.error(e));
                    }

                    // Auto update every 5 sec if just viewing
                    // setInterval(updateHistory, 5000); 

                    formObj.addEventListener('submit', function(e) {
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
                                    localStorage.setItem('backup_active', 'true'); // Flag for global indicator
                                    window.dispatchEvent(new Event('storage')); // Trigger update

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
                                // Parallel Fetch: History & Status
                                updateHistory();

                                fetch("{{ route('backup.progress') }}")
                                    .then(res => res.json())
                                    .then(progress => {
                                        // Update UI
                                        pBar.style.width = progress.percentage + '%';
                                        pPerc.innerText = progress.percentage + '%';
                                        pMsg.innerText = progress.message;

                                        if (progress.status === 'completed') {
                                            clearInterval(interval);
                                            updateHistory(); // Final Update
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
                                            updateHistory(); // Final Update
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
                                    });
                            }, 2000); // Check every 2s for less load since we do double fetch
                        }

                        function resetBtn() {
                            btn.disabled = false;
                            btn.classList.remove('opacity-50', 'cursor-not-allowed');
                            document.getElementById('btn-spinner').classList.add('hidden');
                            document.getElementById('btn-icon').classList.remove('hidden');
                            document.getElementById('btn-text').innerText = 'MULAI UPLOAD KE GOOGLE DRIVE';
                        }
                    });
                });
            </script>
        @endsection
