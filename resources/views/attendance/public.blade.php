<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Koperasi JR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .scanner-container #reader {
            border: none !important;
            border-radius: 1.5rem;
            overflow: hidden;
        }

        .scanner-container #reader__scan_region {
            background: #0f172a !important;
        }

        #reader__dashboard_section_csr button {
            background: #4f46e5 !important;
            color: white !important;
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem !important;
            font-weight: 600 !important;
            margin-top: 1rem !important;
        }
    </style>
</head>

<body class="h-full flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-black text-white tracking-tight mb-2">KOPERASI JR</h1>
            <p class="text-indigo-400 font-bold uppercase tracking-[0.2em] text-sm">Sistem Absensi Digital</p>
        </div>

        <!-- Main Card -->
        <div class="glass-card rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-600/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-emerald-600/20 rounded-full blur-3xl"></div>

            <div class="relative z-10">
                <div class="text-center mb-6">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 bg-white/10 rounded-2xl mb-4 border border-white/20">
                        <svg class="w-8 h-8 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 17h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-white">Scan Barcode Anda</h2>
                    <p class="text-gray-400 text-sm">Arahkan barcode ke kamera untuk absen</p>
                </div>

                <div class="scanner-container mb-8">
                    <div id="reader" class="w-full"></div>
                </div>

                <div id="result-container"
                    class="hidden text-center p-6 rounded-3xl bg-white/10 border border-white/20 animate-pulse">
                    <p class="text-indigo-400 font-black text-lg mb-1">MEMPROSES...</p>
                    <p class="text-white/60 text-sm">Mohon tunggu sebentar</p>
                </div>

                <div class="text-center">
                    <p id="clock" class="text-3xl font-black text-white mb-1">00:00:00</p>
                    <p class="text-gray-500 font-bold text-xs uppercase tracking-widest">
                        {{ Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
            </div>
        </div>

        <div class="mt-8 text-center text-gray-500 text-xs font-bold uppercase tracking-widest">
            &copy; {{ date('Y') }} Koperasi Konsumen Jembar Rahayu Sejahtera
        </div>
    </div>

    <script>
        // Digital Clock
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('clock').textContent = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Scanner Logic
        const html5QrCode = new Html5Qrcode("reader");
        const config = {
            fps: 10,
            qrbox: {
                width: 250,
                height: 250
            }
        };

        let isScanning = true;

        const onScanSuccess = (decodedText, decodedResult) => {
            if (!isScanning) return;
            isScanning = false;

            document.getElementById('reader').classList.add('hidden');
            document.getElementById('result-container').classList.remove('hidden');

            fetch("{{ route('attendance.scan') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        code: decodedText
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: data.type === 'check_in' ? 'ABSEN MASUK' : 'ABSEN PULANG',
                            text: data.message,
                            timer: 3000,
                            showConfirmButton: false,
                            background: '#1e293b',
                            color: '#fff',
                            customClass: {
                                popup: 'rounded-[2rem] border border-white/10',
                                title: 'font-black text-2xl',
                            }
                        }).then(() => {
                            resetScanner();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'GAGAL',
                            text: data.message,
                            timer: 3000,
                            showConfirmButton: false,
                            background: '#1e293b',
                            color: '#fff',
                            customClass: {
                                popup: 'rounded-[2rem] border border-white/10',
                                title: 'font-black text-2xl',
                            }
                        }).then(() => {
                            resetScanner();
                        });
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    resetScanner();
                });
        };

        function resetScanner() {
            document.getElementById('reader').classList.remove('hidden');
            document.getElementById('result-container').classList.add('hidden');
            isScanning = true;
        }

        html5QrCode.start({
            facingMode: "user"
        }, config, onScanSuccess);
    </script>
</body>

</html>
