# FlipPlay REST API — Panduan Setup, phpMyAdmin & Pengujian Postman

Dokumentasi ini berisi langkah-langkah menjalankan proyek **FlipPlay REST API** setelah proses *clone* dari GitHub, menghubungkan database MySQL melalui **phpMyAdmin**, serta menguji seluruh endpoint di **Postman** (`http://127.0.0.1:8000`).

---

## 1. Langkah Menjalankan Proyek Setelah Clone (Terminal)

Karena folder `vendor/` dan file `.env` biasanya diabaikan oleh `.gitignore`, jalankan perintah berikut di terminal setelah meng-*clone* repository:

1. **Clone repository dan masuk ke direktori proyek**:

   ```
   git clone <URL_REPOSITORY_GITHUB>
   cd <nama-folder-proyek>
   
   ```

2. **Install seluruh dependensi PHP menggunakan Composer**:

   ```
   composer install
   
   ```

3. **Salin file `.env.example` menjadi `.env` dan buat `APP_KEY`**:

   ```
   cp .env.example .env
   php artisan key:generate
   
   ```
   *(Di Windows PowerShell, jika `cp` tidak dikenali, gunakan `copy .env.example .env`.)*

4. **Hubungkan database di phpMyAdmin lalu jalankan migrasi** (lihat bagian berikutnya untuk detail phpMyAdmin):

   ```
   php artisan migrate
   
   ```
   *(Jika tersedia seeder untuk data awal, jalankan juga `php artisan db:seed` atau gunakan `php artisan migrate --seed`.)*

5. **Jalankan server pengembangan lokal Laravel**:

   ```
   php artisan serve
   
   ```

---

## 2. Cara Menghubungkan & Membuat Database di phpMyAdmin

Ikuti langkah berikut agar Laravel terhubung ke database lokal melalui **phpMyAdmin** (misalnya menggunakan XAMPP / Laragon):

1. **Jalankan layanan Apache dan MySQL** pada aplikasi server lokal (XAMPP / Laragon).
2. **Buka phpMyAdmin** di browser melalui alamat:

   ```
   http://localhost/phpmyadmin
   
   ```
3. **Buat database baru**:
   * Klik menu **New** / **Baru** di panel kiri.
   * Isi **Database name** (contoh: `flipplay_db`), pilih collation default (`utf8mb4_unicode_ci`), lalu klik **Create** / **Buat**.
4. **Catat kredensial koneksi MySQL**:
   * **Host**: `127.0.0.1` atau `localhost`
   * **Port**: `3306` (default)
   * **Username**: `root` (default)
   * **Password**: kosongkan `""` jika tidak memakai password
5. **Sesuaikan konfigurasi di file `.env` proyek Laravel**:

   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=flipplay_db
   DB_USERNAME=root
   DB_PASSWORD=
   
   ```
6. **Verifikasi koneksi dan struktur tabel**:
   * Jalankan `php artisan migrate` di terminal.
   * Kembali ke **phpMyAdmin**, klik database `flipplay_db`, dan pastikan tabel-tabel Laravel (seperti `users`, `media`, serta tabel relasi koleksi) sudah terbentuk.
   * Pastikan minimal tersedia satu data pengguna uji (misalnya user `Alya` dengan `id: 1`) sebelum menguji endpoint koleksi user.

---

## 3. Persiapan Pengujian di Postman

1. Pastikan `php artisan serve` sedang berjalan (`http://127.0.0.1:8000`).
2. Pada setiap request di Postman, pastikan header berikut aktif:
   * `Accept`: `application/json`
   * `Content-Type`: `application/json` (untuk request yang mengirim *raw JSON body* seperti `POST` dan `PATCH`)

---

## 4. Urutan Skenario & Endpoint Pengujian

### 1. Tambah Media — Uji Validasi Duplikat (`422 Unprocessable Entity`)

* **Method & URL**: `POST http://127.0.0.1:8000/api/media`
* **Body (JSON)**:

  ```
  {
    "title": "Laskar Pelangi",
    "type": "movie",
    "creator": "Riri Riza",
    "year": 2008,
    "duration": 120
  }
  
  ```
* **Ekspektasi**: Jika judul media sudah ada di database, API mengembalikan status **`422`** beserta pesan validasi duplikat.

### 2. Tambah Media Baru (`201 Created`)

* **Method & URL**: `POST http://127.0.0.1:8000/api/media`
* **Body (JSON)**:

  ```
  {
    "title": "Habibie & Ainun",
    "type": "movie",
    "creator": "Faozan Rizal",
    "year": 2012,
    "duration": 118
  }
  
  ```
* **Ekspektasi**: Mengembalikan status **`201`** dan objek `data` media yang baru dibuat (contohnya `id: 2`).

### 3. Cari & Daftar Media dengan Query Parameter (`200 OK`)

* **Method & URL**: `GET http://127.0.0.1:8000/api/media?title=Habibie`
* **Ekspektasi**: Mengembalikan status **`200`** dan menampilkan daftar media yang cocok dengan kata kunci `title`.

### 4. Lihat Detail Satu Media (`200 OK`)

* **Method & URL**: `GET http://127.0.0.1:8000/api/media/2`
* **Ekspektasi**: Mengembalikan status **`200`** dan menampilkan detail lengkap media dengan `id: 2`.

### 5. Perbarui Data Media (`200 OK`)

* **Method & URL**: `PATCH http://127.0.0.1:8000/api/media/2`
* **Body (JSON)**:

  ```
  {
    "duration": 125
  }
  
  ```
* **Ekspektasi**: Mengembalikan status **`200`** dan nilai `duration` pada respons berubah menjadi `125`.

### 6. Tambah Media ke Koleksi User (`201 Created`)

* **Method & URL**: `POST http://127.0.0.1:8000/api/users/1/collection`
* **Body (JSON)**:

  ```
  {
    "media_id": 2
  }
  
  ```
* **Ekspektasi**: Mengembalikan status **`201`** dengan pesan konfirmasi berhasil ditambahkan ke koleksi user serta daftar `collection` terbaru.

### 7. Uji Penolakan Duplikat di Koleksi User (`409 Conflict`)

* **Method & URL**: `POST http://127.0.0.1:8000/api/users/1/collection`
* **Body (JSON)**:

  ```
  {
    "media_id": 2
  }
  
  ```
* **Ekspektasi**: Mengirim ulang request yang sama akan mengembalikan status **`409`** dengan pesan `"Media sudah ada di koleksi."`.

### 8. Lihat Daftar Koleksi User (`200 OK`)

* **Method & URL**: `GET http://127.0.0.1:8000/api/users/1/collection`
* **Ekspektasi**: Mengembalikan status **`200`** dan menampilkan informasi user beserta array `collection` miliknya.

### 9. Hapus Media dari Koleksi User (`200 OK`)

* **Method & URL**: `DELETE http://127.0.0.1:8000/api/users/1/collection/2`
* **Ekspektasi**: Mengembalikan status **`200`** dengan pesan `"Media berhasil dihapus dari koleksi."` dan `collection` kembali kosong (`[]`).

### 10. Hapus Data Media (`200 OK`)

* **Method & URL**: `DELETE http://127.0.0.1:8000/api/media/2`
* **Ekspektasi**: Mengembalikan status **`200`** dengan pesan `"Media berhasil dihapus"`.