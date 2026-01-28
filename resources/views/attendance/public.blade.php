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

        #camera-preview {
            transform: scaleX(-1);
        }

        .location-indicator {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>

<body class="h-full flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-black text-white tracking-tight mb-2">KOPERASI JR</h1>
            <p class="text-indigo-400 font-bold uppercase tracking-[0.2em] text-sm">Sistem Absensi Digital</p>
            @if ($settings && $settings->require_location)
                <div
                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-emerald-500/20 border border-emerald-500/30 rounded-full">
                    <div class="location-indicator w-2 h-2 bg-emerald-400 rounded-full"></div>
                    <span class="text-emerald-300 text-xs font-bold">GPS Tracking Aktif</span>
                </div>
            @endif
            @if ($settings && $settings->require_photo)
                <div
                    class="mt-2 inline-flex items-center gap-2 px-4 py-2 bg-blue-500/20 border border-blue-500/30 rounded-full">
                    <svg class="w-4 h-4 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="text-blue-300 text-xs font-bold">Foto Selfie Diperlukan</span>
                </div>
            @endif
        </div>

        <!-- Main Card -->
        <div class="glass-card rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-600/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-emerald-600/20 rounded-full blur-3xl"></div>

            <div class="relative z-10">
                <!-- Scanner Section -->
                <div id="scanner-section">
                    <div class="text-center mb-6">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-white/10 rounded-2xl mb-4 border border-white/20">
                            <svg class="w-8 h-8 text-indigo-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 17h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-white">Scan Barcode Anda</h2>
                        <p class="text-gray-400 text-sm">Arahkan barcode ke kamera untuk absen</p>
                    </div>

                    <div class="scanner-container mb-6 mx-auto" style="max-width: 400px;">
                        <div id="reader" class="w-full"></div>
                    </div>

                    <!-- GPS Status -->
                    <div id="gps-status" class="hidden mb-4 p-4 bg-white/5 rounded-2xl border border-white/10">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-emerald-500/20 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-white font-bold text-sm">Lokasi Terdeteksi</p>
                                <p id="gps-coords" class="text-gray-400 text-xs"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Camera Section (for selfie) -->
                <div id="camera-section" class="hidden">
                    <div class="text-center mb-6">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-white/10 rounded-2xl mb-4 border border-white/20">
                            <svg class="w-8 h-8 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-white">Ambil Foto Selfie</h2>
                        <p class="text-gray-400 text-sm">Pastikan wajah Anda terlihat jelas</p>
                    </div>

                    <div class="mb-6 mx-auto" style="max-width: 500px;">
                        <video id="camera-preview" class="w-full rounded-2xl" autoplay></video>
                        <canvas id="camera-canvas" class="hidden"></canvas>
                    </div>

                    <button onclick="capturePhoto()"
                        class="w-full bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-black py-4 rounded-2xl hover:shadow-xl hover:shadow-indigo-500/50 transition-all active:scale-95">
                        <svg class="w-6 h-6 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        </svg>
                        AMBIL FOTO
                    </button>
                </div>

                <!-- Processing State -->
                <div id="result-container"
                    class="hidden text-center p-6 rounded-3xl bg-white/10 border border-white/20 animate-pulse">
                    <p class="text-indigo-400 font-black text-lg mb-1">MEMPROSES...</p>
                    <p class="text-white/60 text-sm">Mohon tunggu sebentar</p>
                </div>

                <!-- Clock -->
                <div class="text-center mt-6">
                    <p id="clock" class="text-3xl font-black text-white mb-1">00:00:00</p>
                    <p class="text-gray-500 font-bold text-xs uppercase tracking-widest">
                        {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                    </p>
                </div>

                <!-- Check Status Button -->
                <div class="text-center mt-6">
                    <button onclick="showCheckStatus()"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-white/10 hover:bg-white/20 text-white rounded-2xl font-bold transition-all border border-white/20 hover:border-white/40">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        CEK STATUS ABSENSI
                    </button>
                </div>
            </div>
        </div>

        <!-- Attendance Status Modal -->
        <div id="statusModal" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
            <div class="max-w-md w-full glass-card rounded-[2.5rem] p-8 relative">
                <button onclick="closeStatusModal()"
                    class="absolute top-6 right-6 text-white/60 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="text-center mb-6">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 bg-white/10 rounded-2xl mb-4 border border-white/20">
                        <svg class="w-8 h-8 text-indigo-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-white">Cek Status Absensi</h2>
                    <p class="text-gray-400 text-sm">Scan barcode untuk melihat status absensi hari ini</p>
                </div>

                <div id="statusScanner" class="mb-6 mx-auto" style="max-width: 300px;">
                    <div id="status-reader" class="w-full rounded-2xl overflow-hidden"></div>
                </div>

                <div id="statusResult" class="hidden">
                    <!-- Status will be displayed here -->
                </div>
            </div>
        </div>

        <div class="mt-8 text-center text-gray-500 text-xs font-bold uppercase tracking-widest">
            &copy; {{ date('Y') }} Koperasi Konsumen Jembar Rahayu Sejahtera
        </div>
    </div>

    <script>
        const requireLocation = {{ $settings && $settings->require_location ? 'true' : 'false' }};
        const requirePhoto = {{ $settings && $settings->require_photo ? 'true' : 'false' }};

        let currentPosition = null;
        let scannedCode = null;
        let photoBlob = null;
        let cameraStream = null;

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

        // Get GPS Location
        if (requireLocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    currentPosition = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    };
                    document.getElementById('gps-status').classList.remove('hidden');
                    document.getElementById('gps-coords').textContent =
                        `${position.coords.latitude.toFixed(6)}, ${position.coords.longitude.toFixed(6)}`;
                },
                (error) => {
                    Swal.fire({
                        icon: 'error',
                        title: 'GPS Tidak Aktif',
                        text: 'Aktifkan GPS untuk melanjutkan absensi',
                        background: '#1e293b',
                        color: '#fff',
                    });
                }
            );
        }

        // QR Scanner
        const html5QrCode = new Html5Qrcode("reader");
        const config = {
            fps: 10,
            qrbox: {
                width: 250,
                height: 250
            },
            aspectRatio: 1.0
        };

        let isScanning = true;

        const onScanSuccess = (decodedText, decodedResult) => {
            if (!isScanning) return;
            isScanning = false;

            console.log('QR Code scanned:', decodedText);
            scannedCode = decodedText;

            // Stop scanner
            html5QrCode.stop().then(() => {
                console.log('Scanner stopped successfully');
                // If photo required, show camera
                if (requirePhoto) {
                    document.getElementById('scanner-section').classList.add('hidden');
                    document.getElementById('camera-section').classList.remove('hidden');
                    startCamera();
                } else {
                    // Submit directly
                    submitAttendance();
                }
            }).catch(err => {
                console.error('Error stopping scanner:', err);
                // Continue anyway
                if (requirePhoto) {
                    document.getElementById('scanner-section').classList.add('hidden');
                    document.getElementById('camera-section').classList.remove('hidden');
                    startCamera();
                } else {
                    submitAttendance();
                }
            });
        };

        const onScanError = (errorMessage) => {
            // Ignore scan errors (normal when no QR code detected)
            // Only log critical errors
            if (errorMessage.includes('NotAllowedError') || errorMessage.includes('NotFoundError')) {
                console.error('Camera access error:', errorMessage);
            }
        };

        function startCamera() {
            console.log('Starting selfie camera...');
            navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user'
                    }
                })
                .then(stream => {
                    console.log('Selfie camera started successfully');
                    cameraStream = stream;
                    document.getElementById('camera-preview').srcObject = stream;
                })
                .catch(error => {
                    console.error('Camera error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Kamera Tidak Tersedia',
                        text: 'Izinkan akses kamera untuk melanjutkan. Error: ' + error.message,
                        background: '#1e293b',
                        color: '#fff',
                    });
                });
        }

        function capturePhoto() {
            const video = document.getElementById('camera-preview');
            const canvas = document.getElementById('camera-canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');

            // Flip horizontally
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(video, 0, 0);

            canvas.toBlob((blob) => {
                photoBlob = blob;

                // Stop camera
                if (cameraStream) {
                    cameraStream.getTracks().forEach(track => track.stop());
                }

                submitAttendance();
            }, 'image/jpeg', 0.8);
        }

        function submitAttendance() {
            document.getElementById('scanner-section').classList.add('hidden');
            document.getElementById('camera-section').classList.add('hidden');
            document.getElementById('result-container').classList.remove('hidden');

            const formData = new FormData();
            formData.append('code', scannedCode);

            if (currentPosition) {
                formData.append('latitude', currentPosition.latitude);
                formData.append('longitude', currentPosition.longitude);
            }

            if (photoBlob) {
                formData.append('photo', photoBlob, 'selfie.jpg');
            }

            // Generate device fingerprint
            const deviceId = generateDeviceId();
            formData.append('device_id', deviceId);

            fetch("{{ route('attendance.scan') }}", {
                    method: "POST",
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Convert photo blob to base64 for display
                        const reader = new FileReader();
                        reader.onloadend = function() {
                            const photoDataUrl = reader.result;

                            const statusBadge = data.status === 'late' ?
                                '<span style="background: #ef4444; color: white; padding: 0.5rem 1rem; border-radius: 9999px; font-weight: 800; font-size: 0.75rem;">TERLAMBAT</span>' :
                                '<span style="background: #10b981; color: white; padding: 0.5rem 1rem; border-radius: 9999px; font-weight: 800; font-size: 0.75rem;">TEPAT WAKTU</span>';

                            const distanceInfo = data.distance ?
                                `<div style="margin-top: 0.75rem; padding: 0.75rem; background: rgba(255,255,255,0.05); border-radius: 1rem;">
                                       <span style="color: #9ca3af; font-size: 0.875rem; font-weight: 600;">Jarak dari Kantor:</span>
                                       <span style="color: white; font-weight: 700; margin-left: 0.5rem;">${data.distance}</span>
                                   </div>` :
                                '';

                            Swal.fire({
                                title: data.type === 'check_in' ? '✅ ABSEN MASUK BERHASIL' :
                                    '✅ ABSEN PULANG BERHASIL',
                                html: `
                                    <div style="text-align: center;">
                                        <div style="margin: 1.5rem 0;">
                                            <img src="${photoDataUrl}" 
                                                 alt="Selfie" 
                                                 style="width: 200px; height: 200px; object-fit: cover; border-radius: 1.5rem; border: 3px solid #6366f1; margin: 0 auto; display: block;">
                                        </div>
                                        
                                        <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 1.5rem; margin-top: 1.5rem;">
                                            <div style="margin-bottom: 1rem;">
                                                <h3 style="color: white; font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem;">${data.user}</h3>
                                                ${statusBadge}
                                            </div>
                                            
                                            <div style="margin-top: 1rem; padding: 0.75rem; background: rgba(255,255,255,0.05); border-radius: 1rem;">
                                                <span style="color: #9ca3af; font-size: 0.875rem; font-weight: 600;">Waktu ${data.type === 'check_in' ? 'Masuk' : 'Pulang'}:</span>
                                                <span style="color: #10b981; font-weight: 800; font-size: 1.125rem; margin-left: 0.5rem;">${data.time}</span>
                                            </div>
                                            
                                            ${distanceInfo}
                                        </div>
                                        
                                        <p style="color: #9ca3af; font-size: 0.875rem; margin-top: 1.5rem; font-weight: 600;">
                                            ${data.message}
                                        </p>
                                    </div>
                                `,
                                confirmButtonText: 'TUTUP',
                                confirmButtonColor: '#6366f1',
                                background: '#1e293b',
                                color: '#fff',
                                customClass: {
                                    popup: 'rounded-[2rem] border border-white/10',
                                    confirmButton: 'font-black px-8 py-3 rounded-xl',
                                }
                            }).then(() => {
                                resetScanner();
                            });
                        };

                        if (photoBlob) {
                            reader.readAsDataURL(photoBlob);
                        } else {
                            // Fallback jika tidak ada foto
                            Swal.fire({
                                icon: 'success',
                                title: data.type === 'check_in' ? 'ABSEN MASUK ✓' : 'ABSEN PULANG ✓',
                                text: data.message,
                                timer: 4000,
                                showConfirmButton: true,
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
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'GAGAL',
                            text: data.message,
                            timer: 4000,
                            showConfirmButton: true,
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
                    Swal.fire({
                        icon: 'error',
                        title: 'ERROR',
                        text: 'Terjadi kesalahan sistem. Silakan coba lagi.',
                        background: '#1e293b',
                        color: '#fff',
                    }).then(() => {
                        resetScanner();
                    });
                });
        }

        function generateDeviceId() {
            const ua = navigator.userAgent;
            const screen = `${window.screen.width}x${window.screen.height}`;
            const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            const fingerprint = `${ua}|${screen}|${timezone}`;

            // Simple hash
            let hash = 0;
            for (let i = 0; i < fingerprint.length; i++) {
                const char = fingerprint.charCodeAt(i);
                hash = ((hash << 5) - hash) + char;
                hash = hash & hash;
            }
            return hash.toString(36);
        }

        function resetScanner() {
            scannedCode = null;
            photoBlob = null;

            document.getElementById('scanner-section').classList.remove('hidden');
            document.getElementById('camera-section').classList.add('hidden');
            document.getElementById('result-container').classList.add('hidden');

            isScanning = true;
            console.log('Restarting scanner...');

            // Use user camera (front camera) as requested
            html5QrCode.start({
                    facingMode: "user"
                }, config, onScanSuccess, onScanError)
                .catch(err => {
                    console.error('Failed to start camera:', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Kamera Tidak Dapat Diakses',
                        html: `
                            <p>Tidak dapat mengakses kamera. Pastikan:</p>
                            <ul style="text-align: left; margin: 1rem 0;">
                                <li>• Browser memiliki izin akses kamera</li>
                                <li>• Kamera tidak digunakan aplikasi lain</li>
                                <li>• Gunakan HTTPS atau localhost</li>
                            </ul>
                            <p style="font-size: 0.875rem; margin-top: 1rem;">Error: ${err.message || err}</p>
                        `,
                        background: '#1e293b',
                        color: '#fff',
                        confirmButtonText: 'Coba Lagi',
                    }).then(() => {
                        location.reload(); // Reload page to retry
                    });
                });
        }

        // Start scanner on page load
        console.log('Initializing QR scanner...');

        // Use user camera (front camera) as requested
        html5QrCode.start({
                facingMode: "user"
            }, config, onScanSuccess, onScanError)
            .then(() => {
                console.log('Scanner started successfully with user camera');
            })
            .catch(err => {
                console.error('Failed to start camera:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Kamera Tidak Dapat Diakses',
                    html: `
                        <p>Tidak dapat mengakses kamera. Pastikan:</p>
                        <ul style="text-align: left; margin: 1rem auto; max-width: 300px;">
                            <li>• Browser memiliki izin akses kamera</li>
                            <li>• Kamera tidak digunakan aplikasi lain</li>
                            <li>• Gunakan HTTPS atau localhost</li>
                        </ul>
                        <p style="font-size: 0.875rem; margin-top: 1rem; opacity: 0.7;">Error: ${err.message || err}</p>
                    `,
                    background: '#1e293b',
                    color: '#fff',
                    confirmButtonText: 'Coba Lagi',
                }).then(() => {
                    location.reload(); // Reload page to retry
                });
            });

        // Status Check Functions
        let statusScanner = null;
        let isStatusChecking = false;

        function showCheckStatus() {
            document.getElementById('statusModal').classList.remove('hidden');
            document.getElementById('statusScanner').classList.remove('hidden');
            document.getElementById('statusResult').classList.add('hidden');

            if (!statusScanner) {
                statusScanner = new Html5Qrcode("status-reader");
            }

            isStatusChecking = true;
            const statusConfig = {
                fps: 10,
                qrbox: {
                    width: 200,
                    height: 200
                },
                aspectRatio: 1.0
            };

            statusScanner.start({
                    facingMode: "user"
                },
                statusConfig,
                onStatusScanSuccess,
                onScanError
            ).catch(err => {
                console.error('Failed to start status scanner:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Kamera Tidak Tersedia',
                    text: 'Tidak dapat mengakses kamera untuk cek status.',
                    background: '#1e293b',
                    color: '#fff',
                });
                closeStatusModal();
            });
        }

        function onStatusScanSuccess(decodedText) {
            if (!isStatusChecking) return;
            isStatusChecking = false;

            console.log('Status QR scanned:', decodedText);

            // Stop status scanner
            statusScanner.stop().then(() => {
                document.getElementById('statusScanner').classList.add('hidden');
                loadAttendanceStatus(decodedText);
            }).catch(err => {
                console.error('Error stopping status scanner:', err);
                document.getElementById('statusScanner').classList.add('hidden');
                loadAttendanceStatus(decodedText);
            });
        }

        function loadAttendanceStatus(code) {
            const resultDiv = document.getElementById('statusResult');
            resultDiv.innerHTML = `
                <div class="text-center p-6">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-indigo-500 border-t-transparent"></div>
                    <p class="text-white mt-4 font-bold">Memuat data...</p>
                </div>
            `;
            resultDiv.classList.remove('hidden');

            fetch("{{ route('attendance.check-status') }}", {
                    method: "POST",
                    headers: {
                        "Accept": "application/json",
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        code: code
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayAttendanceStatus(data.data);
                    } else {
                        resultDiv.innerHTML = `
                        <div class="text-center p-6">
                            <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <p class="text-white font-bold mb-2">${data.message}</p>
                            <button onclick="retryStatusCheck()" class="mt-4 px-6 py-2 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700">
                                Scan Lagi
                            </button>
                        </div>
                    `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    resultDiv.innerHTML = `
                    <div class="text-center p-6">
                        <p class="text-red-400 font-bold">Terjadi kesalahan. Silakan coba lagi.</p>
                        <button onclick="retryStatusCheck()" class="mt-4 px-6 py-2 bg-indigo-600 text-white rounded-xl font-bold">
                            Scan Lagi
                        </button>
                    </div>
                `;
                });
        }

        function displayAttendanceStatus(data) {
            const statusBadge = data.status === 'present' ?
                '<span class="px-4 py-2 bg-emerald-500 text-white text-xs font-black uppercase rounded-full">TEPAT WAKTU</span>' :
                '<span class="px-4 py-2 bg-red-500 text-white text-xs font-black uppercase rounded-full">TERLAMBAT</span>';

            const checkOutInfo = data.check_out ?
                `<div class="flex items-center justify-between p-4 bg-white/5 rounded-xl">
                       <span class="text-gray-400 text-sm font-bold">Waktu Pulang</span>
                       <span class="text-white font-bold">${data.check_out}</span>
                   </div>` :
                `<div class="text-center p-4 bg-white/5 rounded-xl">
                       <span class="text-gray-400 text-sm font-bold">Belum absen pulang</span>
                   </div>`;

            document.getElementById('statusResult').innerHTML = `
                <div class="space-y-4">
                    <div class="text-center pb-4 border-b border-white/10">
                        <div class="w-20 h-20 bg-indigo-500 rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="text-2xl font-black text-white">${data.user_name.charAt(0)}</span>
                        </div>
                        <h3 class="text-xl font-black text-white mb-1">${data.user_name}</h3>
                        <p class="text-gray-400 text-sm font-bold uppercase">${data.user_role}</p>
                        <div class="mt-3">${statusBadge}</div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-4 bg-white/5 rounded-xl">
                            <span class="text-gray-400 text-sm font-bold">Tanggal</span>
                            <span class="text-white font-bold">${data.date}</span>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-white/5 rounded-xl">
                            <span class="text-gray-400 text-sm font-bold">Waktu Masuk</span>
                            <span class="text-emerald-400 font-bold">${data.check_in}</span>
                        </div>
                        ${checkOutInfo}
                        ${data.distance ? `
                                            <div class="flex items-center justify-between p-4 bg-white/5 rounded-xl">
                                                <span class="text-gray-400 text-sm font-bold">Jarak dari Kantor</span>
                                                <span class="text-white font-bold">${data.distance}</span>
                                            </div>
                                            ` : ''}
                    </div>

                    <button onclick="closeStatusModal()" class="w-full mt-6 px-6 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-black rounded-2xl hover:shadow-xl transition-all">
                        TUTUP
                    </button>
                </div>
            `;
        }

        function retryStatusCheck() {
            document.getElementById('statusScanner').classList.remove('hidden');
            document.getElementById('statusResult').classList.add('hidden');
            isStatusChecking = true;

            const statusConfig = {
                fps: 10,
                qrbox: {
                    width: 200,
                    height: 200
                },
                aspectRatio: 1.0
            };

            statusScanner.start({
                    facingMode: "environment"
                },
                statusConfig,
                onStatusScanSuccess,
                onScanError
            ).catch(err => {
                return statusScanner.start({
                        facingMode: "user"
                    },
                    statusConfig,
                    onStatusScanSuccess,
                    onScanError
                );
            });
        }

        function closeStatusModal() {
            if (statusScanner && isStatusChecking) {
                try {
                    statusScanner.stop().catch(err => console.warn('Scanner stop error:', err));
                } catch (e) {
                    console.warn('Scanner sync stop error:', e);
                }
            }
            isStatusChecking = false;
            document.getElementById('statusModal').classList.add('hidden');
            document.getElementById('statusResult').innerHTML = '';

            // Re-enable main scanner if it was running
            if (isScanning && typeof html5QrCode !== 'undefined' && html5QrCode.getState() !== 3) {
                // Try to restart main scanner if needed
            }
        }
    </script>
</body>

</html>
