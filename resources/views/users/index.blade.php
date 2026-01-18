@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Manajemen User</h1>
                <p class="text-gray-500 font-medium">Kelola akses dan peran pengguna aplikasi.</p>
            </div>
            <div>
                <a href="{{ route('users.create') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95 gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    TAMBAH USER
                </a>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th
                                class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">
                                Nama</th>
                            <th
                                class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">
                                Email</th>
                            <th
                                class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">
                                Kode Unik</th>
                            <th
                                class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">
                                Role</th>
                            <th
                                class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">
                                Gaji Harian</th>
                            <th
                                class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100 text-right">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-50/30 transition-colors">
                                <td class="px-8 py-6">
                                    <span
                                        class="font-bold text-gray-900 uppercase tracking-tight">{{ $user->name }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-gray-500 font-medium lowercase">{{ $user->email }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="font-black text-indigo-600 bg-indigo-50 px-3 py-1 rounded-lg text-xs">{{ $user->unique_code }}</span>
                                        <button onclick="showQR('{{ $user->unique_code }}', '{{ $user->name }}')"
                                            class="p-1.5 text-gray-400 hover:text-indigo-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 17h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    @php
                                        $roleClass = match ($user->role) {
                                            'super_admin' => 'bg-red-50 text-red-700 ring-red-100',
                                            'ketua' => 'bg-amber-50 text-amber-700 ring-amber-100',
                                            default => 'bg-indigo-50 text-indigo-700 ring-indigo-100',
                                        };
                                        $roleLabel = match ($user->role) {
                                            'super_admin' => 'SUPER ADMIN',
                                            'ketua' => 'KETUA',
                                            'pegawai' => 'PEGAWAI',
                                            default => strtoupper($user->role),
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black ring-1 ring-inset {{ $roleClass }}">
                                        {{ $roleLabel }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <span
                                        class="font-bold text-gray-900">Rp{{ number_format($user->daily_salary, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('users.edit', $user->id) }}"
                                            class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all shadow-sm">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </a>
                                        @if ($user->id !== auth()->id())
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                onsubmit="return confirm('Hapus user ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-red-600 hover:bg-red-50 rounded-xl transition-all shadow-sm">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function showQR(code, name) {
            Swal.fire({
                title: '<span class="text-2xl font-black uppercase tracking-tight">' + name + '</span>',
                html: `
                <div class="flex flex-col items-center p-4">
                    <div class="bg-white p-4 rounded-3xl shadow-inner mb-4 border border-gray-100">
                        <img id="qr-image" src="/qr-code/user/${code}" class="w-64 h-64" alt="QR Code">
                    </div>
                    <p class="text-indigo-600 font-black text-xl tracking-widest">${code}</p>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mt-2 mb-6">Scan untuk absensi</p>
                    
                    <button onclick="downloadQRAsJPG('${code}', '${name}')" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white rounded-xl font-black text-xs tracking-widest hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-100">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M7.5 12l4.5 4.5m0 0l4.5-4.5M12 3v13.5" />
                        </svg>
                        DOWNLOAD QR (JPG)
                    </button>
                </div>
            `,
                showConfirmButton: true,
                confirmButtonText: 'TUTUP',
                confirmButtonColor: '#4f46e5',
                customClass: {
                    popup: 'rounded-[2.5rem]',
                    confirmButton: 'rounded-2xl px-8 py-3 font-black tracking-widest'
                }
            });
        }

        function downloadQRAsJPG(code, name) {
            const img = document.getElementById('qr-image');
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            // High resolution
            const size = 1200;
            canvas.width = size;
            canvas.height = size;

            // White background
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, size, size);

            const tempImg = new Image();
            tempImg.crossOrigin = 'anonymous';

            // We need to fetch the SVG text and data-url it to ensure canvas can draw it without CORS issues
            // and with proper width/height
            fetch(img.src)
                .then(response => response.text())
                .then(svgData => {
                    const svgBlob = new Blob([svgData], {
                        type: 'image/svg+xml;charset=utf-8'
                    });
                    const url = URL.createObjectURL(svgBlob);

                    tempImg.onload = function() {
                        // Draw with some padding
                        ctx.drawImage(tempImg, 100, 100, size - 200, size - 200);

                        const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
                        const link = document.createElement('a');
                        link.download = `QR_${code}_${name.replace(/\s+/g, '_')}.jpg`;
                        link.href = dataUrl;
                        link.click();

                        URL.revokeObjectURL(url);
                    };
                    tempImg.src = url;
                });
        }
    </script>
@endpush
