@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('salaries.index') }}"
                class="inline-flex items-center text-sm font-bold text-indigo-600 hover:text-indigo-700 transition-all gap-2 mb-4">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                KEMBALI KE DAFTAR
            </a>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Buat Slip Gaji Baru</h1>
            <p class="text-gray-500 font-medium">Input data pembayaran gaji untuk pegawai.</p>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 lg:p-12">
            <form action="{{ route('salaries.store') }}" method="POST" class="space-y-8" id="salaryForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <label class="block text-sm font-black text-gray-900 uppercase tracking-widest pl-1">Pegawai</label>
                        <div class="relative">
                            <select name="user_id" id="user_id" required
                                class="block w-full rounded-2xl border-2 border-gray-100 py-4 px-6 text-gray-900 shadow-sm focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-bold appearance-none">
                                <option value="" data-salary="0">Pilih Pegawai...</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" data-salary="{{ $user->daily_salary }}"
                                        {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ strtoupper($user->name) }} ({{ strtoupper($user->role) }})</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-sm font-black text-gray-900 uppercase tracking-widest pl-1">Mulai
                            Tanggal</label>
                        <input type="date" name="start_date" id="start_date"
                            value="{{ old('start_date', now()->format('Y-m-d')) }}" required
                            class="block w-full rounded-2xl border-2 border-gray-100 py-4 px-6 text-gray-900 shadow-sm focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-bold date-calc">
                    </div>

                    <div class="space-y-4">
                        <label class="block text-sm font-black text-gray-900 uppercase tracking-widest pl-1">Sampai
                            Tanggal</label>
                        <input type="date" name="end_date" id="end_date"
                            value="{{ old('end_date', now()->format('Y-m-d')) }}" required
                            class="block w-full rounded-2xl border-2 border-gray-100 py-4 px-6 text-gray-900 shadow-sm focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-bold date-calc">
                    </div>

                    <div class="space-y-4">
                        <label class="block text-sm font-black text-gray-900 uppercase tracking-widest pl-1">Gaji Harian
                            (Rp)</label>
                        <input type="number" name="daily_salary" id="daily_salary" value="{{ old('daily_salary', 0) }}"
                            required
                            class="block w-full rounded-2xl border-2 border-gray-100 py-4 px-6 text-gray-900 shadow-sm focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-bold calc-salary">
                    </div>

                    <div class="space-y-4">
                        <label class="block text-sm font-black text-gray-900 uppercase tracking-widest pl-1">Bonus/Insentif
                            (Rp)</label>
                        <input type="number" name="bonus" id="bonus" value="{{ old('bonus', 0) }}" required
                            class="block w-full rounded-2xl border-2 border-gray-100 py-4 px-6 text-emerald-600 shadow-sm focus:ring-8 focus:ring-emerald-500/5 focus:border-emerald-500 transition-all font-bold calc-salary">
                    </div>

                    <div class="space-y-4">
                        <label class="block text-sm font-black text-gray-900 uppercase tracking-widest pl-1">Potongan
                            (Rp)</label>
                        <input type="number" name="deductions" id="deductions" value="{{ old('deductions', 0) }}" required
                            class="block w-full rounded-2xl border-2 border-gray-100 py-4 px-6 text-red-600 shadow-sm focus:ring-8 focus:ring-red-500/5 focus:border-red-500 transition-all font-bold calc-salary">
                    </div>

                    <div class="col-span-full">
                        <div class="space-y-4">
                            <label class="block text-sm font-black text-gray-900 uppercase tracking-widest pl-1">Pilih Hari
                                Kerja (Manual / Dari Absensi)</label>
                            <div class="bg-gray-50 rounded-3xl p-6 border-2 border-gray-100">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex gap-2">
                                        <button type="button" onclick="selectAllDates(true)"
                                            class="text-[10px] font-black bg-indigo-100 text-indigo-700 px-3 py-1.5 rounded-lg hover:bg-indigo-200 transition-all uppercase tracking-widest">Pilih
                                            Semua</button>
                                        <button type="button" onclick="selectAllDates(false)"
                                            class="text-[10px] font-black bg-gray-200 text-gray-600 px-3 py-1.5 rounded-lg hover:bg-gray-300 transition-all uppercase tracking-widest">Hapus
                                            Semua</button>
                                    </div>
                                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                        id="attendance_status">
                                        Menunggu input...
                                    </div>
                                </div>

                                <div id="date_checkboxes"
                                    class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                                    <!-- Dynamic checkboxes here -->
                                    <p class="col-span-full text-center text-gray-400 py-4 font-medium italic">Silakan pilih
                                        Pegawai dan Rentang Tanggal...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="working_days" id="working_days" value="0">

                    <div class="col-span-full">
                        <div
                            class="bg-gray-50 rounded-3xl p-6 border-2 border-dashed border-gray-200 flex flex-wrap gap-8 justify-center">
                            <div class="text-center">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Hari
                                    Kerja</p>
                                <p id="working_days_display" class="text-2xl font-black text-gray-900">0 Hari</p>
                            </div>
                            <div class="text-center">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Gaji
                                    Pokok</p>
                                <p id="base_salary_display" class="text-2xl font-black text-gray-900">Rp0</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-sm font-black text-gray-900 uppercase tracking-widest pl-1">Status
                            Pembayaran</label>
                        <div class="relative">
                            <select name="status" required
                                class="block w-full rounded-2xl border-2 border-gray-100 py-4 px-6 text-gray-900 shadow-sm focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-bold appearance-none">
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>BELUM DIBAYAR
                                    (PENDING)</option>
                                <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>SUDAH DIBAYAR
                                    (LUNAS)</option>
                            </select>
                            <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-full">
                        <div
                            class="bg-indigo-600 rounded-3xl p-8 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-2xl shadow-indigo-200">
                            <div>
                                <p class="text-indigo-200 text-xs font-black uppercase tracking-widest mb-1">Total Gaji
                                    Bersih</p>
                                <h2 id="net_salary_display" class="text-4xl font-black text-white tracking-tighter">Rp0
                                </h2>
                            </div>
                            <div
                                class="md:text-right text-indigo-200 text-[10px] font-bold uppercase leading-relaxed max-w-xs">
                                <p>Gaji Bersih = (Gaji Harian × Hari Kerja) + Bonus - Potongan</p>
                                <p class="mt-1 text-white/50">* Otomatis dihitung dari data absensi</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-full space-y-4">
                        <label class="block text-sm font-black text-gray-900 uppercase tracking-widest pl-1">Catatan
                            Tambahan</label>
                        <textarea name="notes" rows="3" placeholder="Contoh: Pembayaran periode 1-15 Jan..."
                            class="block w-full rounded-2xl border-2 border-gray-100 py-4 px-6 text-gray-900 shadow-sm focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-bold placeholder:text-gray-300">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full py-5 bg-indigo-600 text-white font-black rounded-3xl shadow-2xl shadow-indigo-200 hover:bg-indigo-700 transition-all active:scale-[0.98] uppercase tracking-[0.2em]">
                        Simpan Slip Gaji
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let attendanceDates = [];

        async function fetchAttendance() {
            const userId = document.getElementById('user_id').value;
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            if (!userId || !startDate || !endDate) return;

            const statusEl = document.getElementById('attendance_status');
            statusEl.innerText = 'Memuat data absensi...';
            statusEl.className = 'text-[10px] font-black text-indigo-500 uppercase tracking-widest';

            try {
                const response = await fetch(
                    `{{ route('attendance.present-dates') }}?user_id=${userId}&start_date=${startDate}&end_date=${endDate}`
                    );
                const data = await response.json();

                if (data.success) {
                    attendanceDates = data.dates; // dates is an array of "YYYY-MM-DD"
                    generateDateCheckboxes();
                    statusEl.innerText = 'Data siap (Berdasarkan Absensi)';
                    statusEl.className = 'text-[10px] font-black text-emerald-500 uppercase tracking-widest';
                }
            } catch (error) {
                console.error('Fetch error:', error);
                statusEl.innerText = 'Gagal memuat absensi';
                statusEl.className = 'text-[10px] font-black text-red-500 uppercase tracking-widest';
                generateDateCheckboxes(); // Fallback to manual
            }
        }

        function generateDateCheckboxes() {
            const startDateArr = document.getElementById('start_date').value.split('-');
            const endDateArr = document.getElementById('end_date').value.split('-');

            if (startDateArr.length !== 3 || endDateArr.length !== 3) return;

            const startDate = new Date(startDateArr[0], startDateArr[1] - 1, startDateArr[2]);
            const endDate = new Date(endDateArr[0], endDateArr[1] - 1, endDateArr[2]);
            const container = document.getElementById('date_checkboxes');

            container.innerHTML = '';

            if (startDate > endDate) {
                container.innerHTML =
                    '<p class="col-span-full text-center text-red-400 py-4 font-bold italic">Tanggal mulai tidak boleh lebih besar dari tanggal selesai.</p>';
                return;
            }

            let current = new Date(startDate);
            const daysNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

            while (current <= endDate) {
                const dateStr = current.toISOString().split('T')[0];
                const dayNum = current.getDay();
                const dayName = daysNames[dayNum];

                // If we have real attendance data, check if this date is in the list
                let isChecked = false;
                if (attendanceDates.length > 0) {
                    isChecked = attendanceDates.includes(dateStr);
                } else {
                    // Fallback to default rule: Exclude Sunday (0) and Saturday (6) or whatever common rule
                    // Let's stick to the previous rule or just uncheck all if no data
                    isChecked = (dayNum !== 0 && dayNum !== 6);
                }

                const div = document.createElement('div');
                div.className = 'relative group';
                div.innerHTML = `
                    <input type="checkbox" id="date_${dateStr}" value="${dateStr}" 
                        ${isChecked ? 'checked' : ''} 
                        class="peer hidden date-item-checkbox" 
                        onchange="updateWorkingDaysCount()">
                    <label for="date_${dateStr}" class="block p-3 rounded-xl border-2 border-gray-100 bg-white peer-checked:border-indigo-600 peer-checked:bg-indigo-50 cursor-pointer hover:border-indigo-300 transition-all select-none">
                        <p class="text-[10px] font-black text-gray-400 peer-checked:text-indigo-600 uppercase tracking-tighter">${dayName}</p>
                        <p class="text-sm font-bold text-gray-900">${current.getDate()} ${current.toLocaleString('id-ID', { month: 'short' })}</p>
                    </label>
                `;
                container.appendChild(div);

                current.setDate(current.getDate() + 1);
            }
            updateWorkingDaysCount();
        }

        function selectAllDates(status) {
            document.querySelectorAll('.date-item-checkbox').forEach(cb => {
                cb.checked = status;
            });
            updateWorkingDaysCount();
        }

        function updateWorkingDaysCount() {
            const checked = document.querySelectorAll('.date-item-checkbox:checked');
            const count = checked.length;

            document.getElementById('working_days').value = count;
            document.getElementById('working_days_display').innerText = count + ' Hari';

            calculateFinalSalary();
        }

        function calculateFinalSalary() {
            const workingDays = parseInt(document.getElementById('working_days').value) || 0;
            const daily = parseFloat(document.getElementById('daily_salary').value) || 0;
            const bonus = parseFloat(document.getElementById('bonus').value) || 0;
            const deductions = parseFloat(document.getElementById('deductions').value) || 0;

            const baseSalary = daily * workingDays;
            const netSalary = baseSalary + bonus - deductions;

            document.getElementById('base_salary_display').innerText = 'Rp' + baseSalary.toLocaleString('id-ID');
            document.getElementById('net_salary_display').innerText = 'Rp' + netSalary.toLocaleString('id-ID');
        }

        document.getElementById('user_id').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const salary = selected.getAttribute('data-salary') || 0;
            document.getElementById('daily_salary').value = salary;
            generateDateCheckboxes();
        });

        document.querySelectorAll('.date-calc').forEach(input => {
            input.addEventListener('change', generateDateCheckboxes);
        });

        document.querySelectorAll('.calc-salary').forEach(input => {
            input.addEventListener('input', calculateFinalSalary);
        });

        // Initialize
        if (document.getElementById('user_id').value) {
            generateDateCheckboxes();
        }
    </script>
@endsection
