# E-Budgeting System 📊

E-Budgeting System adalah aplikasi berbasis web yang dibangun menggunakan **Laravel** untuk mengelola, melacak, dan menyetujui pengajuan anggaran (budget) secara digital di dalam perusahaan. Sistem ini mempermudah alur birokrasi pengajuan dana melalui tahapan yang terstruktur dan termonitor dengan baik.

## 🌟 Fitur Utama

- **Role-Based Access Control (RBAC):** Akses sistem dibagi berdasarkan peran (Superadmin, Director, Kepala Departemen, Kepala Divisi, Accounting, Staff, dll).
- **Alur Persetujuan Bertingkat:** 
  1. Permintaan Pengadaan Barang/Jasa (PPBJ)
  2. Proposal Harga (PH)
  3. Internal Agreement (IA)
- **Monitoring Real-time:** Dashboard khusus untuk memonitor status pengajuan dan sisa pagu anggaran (budget) masing-masing departemen.
- **Export/Cetak Dokumen:** Fitur untuk mengunduh dokumen pengajuan dalam format PDF dan upload template Excel.
- **Keamanan & Validasi:** Validasi ketat terhadap nominal anggaran agar tidak melebihi sisa pagu, serta pembatasan hak akses (approval) antar departemen.
- **Automated Testing:** Terintegrasi dengan TestSprite untuk pengujian otomatis (Frontend & Backend).

## 🚀 Teknologi yang Digunakan

- **Framework:** Laravel (PHP)
- **Frontend:** Blade Templating, TailwindCSS, Vanilla JavaScript
- **Database:** MySQL
- **Testing:** PHPUnit, TestSprite AI Agent

## 🛠️ Instalasi di Komputer Lokal (Development)

Ikuti langkah-langkah berikut untuk menjalankan project ini di komputer Anda:

1. **Clone repository ini:**
   ```bash
   git clone https://github.com/Yusadinar/e-budgeting-system.git
   cd e-budgeting-system
   ```

2. **Install dependencies (PHP & Node.js):**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Konfigurasi Environment:**
   Copy file `.env.example` menjadi `.env` lalu sesuaikan konfigurasi database Anda.
   ```bash
   cp .env.example .env
   ```
   *Note: Pastikan mengatur `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD`.*

4. **Generate App Key & Setup Database:**
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```
   *(Catatan: `--seed` akan memasukkan data dummy (seeder) seperti user, departemen, dll).*

5. **Buat Symlink Storage (Untuk Gambar/PDF):**
   ```bash
   php artisan storage:link
   ```

6. **Jalankan Aplikasi:**
   ```bash
   php artisan serve
   ```
   Buka browser dan akses: `http://localhost:8000`

## 🌍 Panduan Deployment (Shared Hosting)

Project ini telah dilengkapi dengan file `.htaccess` di *root directory* agar mudah di-deploy ke *shared hosting* (seperti InfinityFree, Niagahoster, dll) tanpa perlu memodifikasi *core files* Laravel.

1. Upload **seluruh isi project** ke folder `htdocs` atau `public_html` di hosting Anda.
2. Buat database di cPanel dan import file database `.sql` Anda.
3. Sesuaikan file `.env` dengan kredensial database di hosting:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=http://domain-anda.com
   ```
4. Website siap diakses! (File `.htaccess` akan otomatis mengarahkan *traffic* ke folder `public/`).

## 👨‍💻 Kontributor

- **Yusadinar** - *Lead Developer*
