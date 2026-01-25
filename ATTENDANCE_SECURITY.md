# Sistem Absensi Anti-Manipulasi - Koperasi JR

## 📋 Fitur Keamanan

### 1. **GPS Geofencing** 🌍

- Validasi lokasi real-time menggunakan koordinat GPS
- Perhitungan jarak menggunakan formula Haversine (akurasi tinggi)
- Konfigurasi radius kantor yang dapat disesuaikan (default: 100 meter)
- Pencatatan jarak actual dari titik kantor
- **Anti-manipulasi**: Tidak bisa absen dari lokasi lain

### 2. **Photo Selfie Verification** 📸

- Wajib ambil foto selfie saat check-in/check-out (opsional, bisa diaktifkan)
- Foto disimpan dengan timestamp dan user ID
- Format: `check_in_{user_id}_{timestamp}.jpg`
- Lokasi penyimpanan: `storage/app/public/attendance/photos/`
- **Anti-manipulasi**: Bukti visual kehadiran fisik

###3. **IP Address & Device Tracking** 🖥️

- Pencatatan IP address saat check-in dan check-out
- User agent tracking (browser, OS, device info)
- Device fingerprinting berdasarkan:
    - User Agent
    - Screen resolution
    - Timezone
    - Browser language
- **Anti-manipulasi**: Deteksi jika menggunakan perangkat berbeda

### 4. **Strict Time Validation** ⏰

- Tidak bisa check-in lebih dari 1 jam sebelum jadwal
- Tidak bisa check-out sebelum jam pulang
- Status otomatis: "present" atau "late" berdasarkan waktu
- **Anti-manipulasi**: Mencegah absen terlalu dini

### 5. **Manual Entry dengan Approval System** ✅

- Admin dapat menambah absensi manual
- Setiap entry manual harus diberi alasan koreksi
- Tercatat siapa yang approve dan kapan
- Flag `is_manual_entry` untuk tracking
- **Anti-manipulasi**: Audit trail lengkap

### 6. **One-Time Attendance** 🔒

- Satu user hanya bisa check-in sekali per hari
- Check-out hanya bisa dilakukan setelah check-in
- Tidak bisa check-in/out dua kali
- **Anti-manipulasi**: Prevent double attendance

## 🗄️ Struktur Database

### Tabel: `attendances`

```sql
- user_id (foreign key)
- date
- check_in, check_out (time)
- status ('present', 'late', 'absent')

-- GPS Location
- check_in_latitude, check_in_longitude
- check_out_latitude, check_out_longitude
- check_in_distance, check_out_distance (meters)

-- Photo Verification
- check_in_photo, check_out_photo (file path)

-- IP & Device Tracking
- check_in_ip, check_out_ip
- check_in_user_agent, check_out_user_agent
- check_in_device_id, check_out_device_id

-- Approval System
- is_manual_entry (boolean)
- approved_by (foreign key to users)
- approved_at (timestamp)
- correction_reason (text)
```

### Tabel: `attendance_settings`

```sql
- check_in_time, check_out_time
- office_latitude, office_longitude
- allowed_radius (meters)
- require_photo (boolean)
- require_location (boolean)
- strict_time (boolean)
```

## 🔧 Konfigurasi

### Mengaktifkan Geofencing

1. Login sebagai admin
2. Buka menu "Pengaturan Absensi"
3. Aktifkan "Wajib Lokasi" (Require Location)
4. Set koordinat kantor (latitude, longitude)
5. Set radius maksimal yang diizinkan (contoh: 100 meter)

### Mengaktifkan Photo Selfie

1. Buka "Pengaturan Absensi"
2. Aktifkan "Wajib Foto" (Require Photo)
3. User akan diminta foto selfie saat scan QR

### Mengatur Jam Kerja

1. Set "Jam Masuk" dan "Jam Pulang"
2. Aktifkan "Strict Time" untuk mencegah absen terlalu dini

## 📱 Cara Penggunaan

### Untuk Karyawan:

1. Buka halaman absensi publik
2. Izinkan akses GPS (jika diaktifkan)
3. Scan QR code dari kartu barcode
4. Ambil foto selfie (jika diperlukan)
5. Sistem otomatis validasi dan menyimpan

### Untuk Admin:

1. Monitor absensi real-time di halaman "Laporan Absensi"
2. Lihat detail keamanan: GPS, foto, IP, device
3. Buat entry manual jika diperlukan (dengan alasan)
4. Export laporan untuk analisis

## 🛡️ Keamanan yang Diterapkan

### Level 1: Lokasi

✅ GPS validation
✅ Geofencing dengan radius
✅ Perhitungan jarak akurat

### Level 2: Visual

✅ Photo selfie requirement
✅ Timestamp pada nama file
✅ Storage terorganisir

### Level 3: Identitas Digital

✅ IP address logging
✅ User agent tracking
✅ Device fingerprinting
✅ Cross-device detection

### Level 4: Temporal

✅ Strict time validation
✅ Anti-backdating
✅ One-time per day

### Level 5: Audit

✅ Manual entry approval
✅ Correction reasons
✅ Audit trail lengkap
✅ Who, what, when tracking

## 🔍 Monitoring & Audit

### Data yang Tercatat:

- **Siapa**: User ID, nama
- **Kapan**: Tanggal, jam check-in/out
- **Dimana**: GPS coordinates, jarak dari kantor
- **Dengan Apa**: Device ID, IP, user agent
- **Bukti**: Foto selfie (opsional)
- **Status**: Present, late, atau manual entry

### Red Flags untuk Diwaspadai:

🚩 Check-in dari IP address berbeda setiap hari
🚩 Jarak GPS terlalu jauh tapi tercatat
🚩 Device fingerprint berubah-ubah
🚩 Banyak manual entry tanpa alasan jelas
🚩 Pola waktu yang suspicious

## 📊 Pelaporan

### Halaman Report Attendance akan Menampilkan:

- Tabel lengkap dengan filter tanggal & user
- Badge status (hijau/kuning/merah)
- Indikator manual entry
- Link detail untuk melihat:
    - GPS coordinates
    - Foto check-in/out
    - IP dan device info
    - Approval history

## 🚀 Migration & Setup

```bash
# Jalankan migration
php artisan migrate

# Konfigurasi storage link (untuk foto)
php artisan storage:link

# Set permission folder
chmod -R 775 storage/app/public/attendance
```

## ⚠️ Catatan Penting

1. **Privacy**: Informasikan kepada karyawan bahwa GPS dan foto akan dicatat
2. **Storage**: Photo selfie akan memakan space, lakukan cleanup berkala
3. **GPS Accuracy**: Akurasi GPS bisa terpengaruh cuaca dan signal
4. **Browser Permission**: User harus izinkan akses GPS dan camera
5. **HTTPS**: Gunakan HTTPS untuk camera dan GPS API

## 🔐 Best Practices

1. ✅ Review manual entries secara berkala
2. ✅ Monitor suspicious patterns
3. ✅ Backup database attendance regularly
4. ✅ Set radius yang realistis (tidak terlalu ketat)
5. ✅ Edukasi user tentang pentingnya akurasi data
6. ✅ Maintain audit log minimal 1 tahun

## 📞 Support

Jika ada masalah:

1. Check GPS permission
2. Check camera permission
3. Check internet connection
4. Clear browser cache
5. Try different browser
6. Contact IT support

---

**Sistem ini dirancang untuk mencegah manipulasi sambil tetap user-friendly. Kombinasi GPS, photo, IP tracking, dan device fingerprinting membuat hampir mustahil untuk fake attendance!** 🛡️
