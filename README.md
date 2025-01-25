
# Proyek Laravel 10 - Setup dan Konfigurasi

Proyek ini adalah aplikasi berbasis Laravel 10 yang dikonfigurasi untuk bekerja dengan Docker dan database yang telah disiapkan. Ikuti langkah-langkah berikut untuk menyiapkan dan menjalankan proyek ini.

## Prasyarat

- Docker & Docker Compose terpasang di mesin Anda.
- PHP 8.1 atau lebih tinggi (dalam kontainer Docker).
- Laravel 10.

## Langkah-langkah Instalasi

### 1. **Clone Proyek**

Clone repositori ini ke mesin lokal Anda:

```bash
git clone https://github.com/embapge/inventory-service.git
cd inventory-service
```

### 2. **Konfigurasi Docker**

Proyek ini menggunakan Docker dan Docker Compose untuk mempermudah pengelolaan lingkungan pengembangan.

#### a. **Membangun Kontainer Docker**

Jalankan perintah berikut untuk membangun dan menjalankan kontainer Docker:

```bash
docker-compose up -d --build
```

Perintah ini akan membangun dan menjalankan kontainer di background (`-d` flag) dengan file konfigurasi dari `docker-compose.yml`.

#### b. **Mengecek Status Kontainer**

Untuk memeriksa status kontainer, jalankan:

```bash
docker-compose ps
```

### 3. **Konfigurasi Database**

Proyek ini menggunakan database yang sudah dikonfigurasi dalam file `.env`.

#### a. **Menyiapkan `.env`**

Pastikan file `.env` sudah ada di direktori proyek Anda. Jika belum, salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

#### b. **Menyesuaikan Pengaturan Database**

Di dalam file `.env`, periksa dan sesuaikan pengaturan berikut agar sesuai dengan konfigurasi Docker Anda:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

- `DB_HOST` diatur ke `db`, yang merupakan nama layanan database dalam file `docker-compose.yml`.
- Pastikan tidak sedang menjalankan mysql di XAMPP atau Laragon. Karena port akan konflik.
- `DB_USERNAME` dan `DB_PASSWORD` diatur sesuai dengan pengaturan default pada kontainer Docker.

### 4. **Menjalankan Migrations dan Seeder**

Setelah Docker dan database siap, jalankan perintah berikut untuk menjalankan migrasi dan seeder database diakrenakan migrasi dan seeder sudah disiapkan:

```bash
docker-compose exec app php artisan migrate --seed
```

Perintah ini akan:
- Menjalankan migrasi untuk membuat tabel-tabel yang diperlukan.
- Menjalankan seeder untuk mengisi tabel dengan data awal.

### 5. **Akses Aplikasi**

Setelah kontainer berjalan, buka browser dan akses aplikasi melalui:

```
http://localhost:8888
```

### 6. **Dokumentasi API**

Dokumentasi API ada di `swagger.yml`:

## Troubleshooting

- **Masalah Kontainer Tidak Berjalan**: Pastikan Docker dan Docker Compose sudah terpasang dengan benar dan kontainer berjalan dengan lancar.
- **Masalah Koneksi Database**: Pastikan database dalam Docker dapat diakses oleh aplikasi Laravel dengan memeriksa pengaturan `.env`.
- Jangan ragu untuk open issue jika terdapat kendala selama instalasi.

---

## Penutupan

Sekarang aplikasi Laravel Anda siap digunakan! Jangan ragu untuk menyesuaikan konfigurasi lebih lanjut sesuai kebutuhan proyek.
