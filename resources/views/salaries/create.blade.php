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
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
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
                                <p class="mt-1 text-white/50">* Hari Jumat & Sabtu tidak dihitung</p>
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
        function calculateSalary() {
            const startDate = new Date(document.getElementById('start_date').value);
            const endDate = new Date(document.getElementById('end_date').value);
            const daily = parseFloat(document.getElementById('daily_salary').value) || 0;
            const bonus = parseFloat(document.getElementById('bonus').value) || 0;
            const deductions = parseFloat(document.getElementById('deductions').value) || 0;

            let workingDays = 0;
            if (startDate <= endDate) {
                let current = new Date(startDate);
                while (current <= endDate) {
                    const day = current.getDay();
                    // 0: Sun, 1: Mon, 2: Tue, 3: Wed, 4: Thu, 5: Fri, 6: Sat
                    // User want to exclude Friday (5) and Saturday (6)
                    if (day !== 5 && day !== 6) {
                        workingDays++;
                    }
                    current.setDate(current.getDate() + 1);
                }
            }

            const baseSalary = daily * workingDays;
            const netSalary = baseSalary + bonus - deductions;

            document.getElementById('working_days_display').innerText = workingDays + ' Hari';
            document.getElementById('base_salary_display').innerText = 'Rp' + baseSalary.toLocaleString('id-ID');
            document.getElementById('net_salary_display').innerText = 'Rp' + netSalary.toLocaleString('id-ID');
        }

        document.getElementById('user_id').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const salary = selected.getAttribute('data-salary') || 0;
            document.getElementById('daily_salary').value = salary;
            calculateSalary();
        });

        document.querySelectorAll('.calc-salary, .date-calc').forEach(input => {
            input.addEventListener('input', calculateSalary);
        });

        calculateSalary();
    </script>
@endsection
