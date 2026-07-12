# Second Cafe

Aplikasi pemesanan makanan dan minuman berbasis web dengan dukungan QR Code pada Second Cafe. Sistem ini dikembangkan menggunakan Laravel 12 dan Filament 5 sebagai bagian dari penelitian skripsi dengan metode SDLC Waterfall.

Pelanggan dapat memindai QR Code pada meja untuk melihat menu, membuat pesanan, dan melakukan pembayaran digital. Admin/Owner dan Kasir mengelola operasional melalui panel Filament.

---

## Fitur Utama

### Admin/Owner

- Mengelola akun pengguna.
- Mengelola kategori menu.
- Mengelola data makanan dan minuman.
- Mengelola meja dan QR Code.
- Melihat seluruh data pesanan.
- Melihat dan memverifikasi pembayaran.
- Melihat transaksi dan laporan penjualan.

### Kasir

- Melihat pesanan masuk.
- Memverifikasi pembayaran pelanggan.
- Menerima atau membatalkan pesanan.
- Mengubah status proses pesanan.
- Menyelesaikan pesanan.
- Mencetak struk transaksi.

### Pelanggan

- Memindai QR Code meja.
- Melihat menu makanan dan minuman.
- Memilih menu dan mengelola keranjang.
- Melakukan checkout.
- Melakukan pembayaran digital.
- Melihat status pesanan.

---

## Tech Stack

- Laravel 12
- Filament 5.6.7
- Livewire 4
- PHP 8.2 atau lebih baru
- MySQL
- Tailwind CSS 4
- JavaScript
- Vite
- Node.js dan NPM
- Git dan GitHub

---

## Persyaratan Sistem

Pastikan perangkat sudah memiliki:

- PHP minimal versi 8.2.
- Composer.
- MySQL atau MariaDB.
- Node.js dan NPM.
- Laragon, XAMPP, atau web server sejenis.
- Git.

Periksa versi perangkat dengan perintah:

```bash
php -v
composer -V
node -v
npm -v
git --version
```

---

## Cara Menjalankan Project

### 1. Clone Repository

```bash
git clone https://github.com/USERNAME/second-cafe.git
```

Masuk ke folder project:

```bash
cd second-cafe
```

---

### 2. Install Dependency Backend

```bash
composer install
```

---

### 3. Install Dependency Frontend

```bash
npm install
```

---

### 4. Setup Environment

Salin file environment.

#### Linux atau macOS

```bash
cp .env.example .env
```

#### Windows Command Prompt

```bash
copy .env.example .env
```

#### Windows PowerShell

```powershell
Copy-Item .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

---

## Konfigurasi Aplikasi

Buka file `.env`, kemudian sesuaikan konfigurasi berikut:

```env
APP_NAME="Second Cafe"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_FAKER_LOCALE=id_ID
```

---

## Konfigurasi Database

Buat database MySQL dengan nama:

```text
second_cafe
```

Kemudian sesuaikan konfigurasi database pada `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=second_cafe
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migration:

```bash
php artisan migrate
```

Jalankan seeder untuk membuat data awal:

```bash
php artisan db:seed
```

Migration dan seeder dapat dijalankan sekaligus pada database pengembangan baru:

```bash
php artisan migrate:fresh --seed
```

> Perintah `migrate:fresh` akan menghapus seluruh tabel dan data. Jangan menjalankannya pada database yang sudah berisi data penting.

---

## Membuat Storage Link

Jalankan perintah berikut agar gambar menu dapat ditampilkan:

```bash
php artisan storage:link
```

Gambar menu tersimpan di:

```text
storage/app/public/menus
```

---

## Menjalankan Aplikasi

Jalankan server Laravel:

```bash
php artisan serve
```

Pada terminal lain, jalankan Vite:

```bash
npm run dev
```

Akses halaman aplikasi:

```text
http://127.0.0.1:8000
```

Akses panel Admin dan Kasir:

```text
http://127.0.0.1:8000/admin
```

---

## Build Frontend

Untuk membuat file frontend siap produksi:

```bash
npm run build
```

---

## Membersihkan Cache

Jika perubahan belum tampil, jalankan:

```bash
php artisan optimize:clear
```

Kemudian muat ulang halaman dengan:

```text
Ctrl + F5
```

---

## Struktur Pengguna

Sistem memiliki tiga aktor utama:

| Aktor       | Akses                                                                     |
| ----------- | ------------------------------------------------------------------------- |
| Admin/Owner | Mengelola seluruh data master, operasional, transaksi, dan laporan        |
| Kasir       | Mengelola pembayaran dan proses pesanan sekaligus sebagai bagian dapur    |
| Pelanggan   | Mengakses menu, pemesanan, pembayaran, dan status pesanan melalui QR Code |

---

## Struktur Navigasi Panel

```text
Dashboard

Data Master
├── Akun Pengguna
├── Kategori Menu
├── Data Menu
└── Meja dan QR Code

Operasional
├── Pesanan Masuk
└── Pembayaran

Laporan
├── Laporan Penjualan
└── Riwayat Transaksi
```

---

## Workflow Tim Development

### 1. Perbarui branch utama

```bash
git checkout main
git pull origin main
```

### 2. Buat branch baru

```bash
git checkout -b feature/nama-fitur
```

Contoh:

```bash
git checkout -b feature/menu
```

### 3. Simpan perubahan

```bash
git add .
git commit -m "Menambahkan fitur pengelolaan menu"
```

### 4. Push branch

```bash
git push origin feature/menu
```

### 5. Buat Pull Request

Setelah branch dikirim:

1. Buka repository di GitHub.
2. Buat Pull Request menuju branch `main`.
3. Periksa kembali perubahan kode.
4. Tunggu proses review.
5. Merge setelah perubahan disetujui.

---

## Aturan Project

- Tidak melakukan push langsung ke branch `main`.
- Setiap fitur dibuat pada branch tersendiri.
- Setiap perubahan digabungkan melalui Pull Request.
- Perubahan database wajib menggunakan migration.
- Data awal wajib dibuat menggunakan seeder.
- Jangan mengunggah file `.env` ke repository.
- Jangan menyimpan password atau data rahasia di source code.
- Jalankan pengujian sebelum membuat Pull Request.
- Gunakan nama variabel, model, dan migration yang konsisten.
- Gunakan bahasa Indonesia untuk label antarmuka aplikasi.

---

## Pengujian

Sistem diuji menggunakan metode Black Box Testing. Fungsi yang diuji meliputi:

- Login Admin dan Kasir.
- Pengelolaan akun pengguna.
- Pengelolaan kategori.
- Pengelolaan menu dan gambar.
- Pengelolaan meja dan QR Code.
- Pemesanan oleh pelanggan.
- Pembayaran digital.
- Verifikasi pembayaran.
- Perubahan status pesanan.
- Pembuatan laporan penjualan.

Jalankan pengujian otomatis Laravel dengan:

```bash
php artisan test
```

---

## Metode Pengembangan

Project dikembangkan menggunakan metode Software Development Life Cycle dengan model Waterfall:

1. Perencanaan.
2. Analisis kebutuhan.
3. Perancangan sistem.
4. Implementasi.
5. Pengujian.
6. Pemeliharaan.

---

## Developer

**Yogi Sepdu Dehiya**

---

## Objek Penelitian

**Second Cafe**  
Kabupaten Merangin, Provinsi Jambi
