# Terra Travel

Aplikasi agen perjalanan berbasis Laravel: katalog paket wisata, pemesanan,
pembayaran, dan dashboard terpisah untuk admin, manager, tour guide, dan user/customer.

## Kebutuhan

- PHP 8.1+ dengan ekstensi `gd` (dipakai untuk mengecilkan foto contoh)
- Composer
- Node.js 18+ dan npm
- MySQL

## Instalasi

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate
```

Sesuaikan koneksi database di `.env`, lalu:

```bash
php artisan migrate:fresh --seed
php artisan storage:link
npm run build
php artisan serve
```

`php artisan storage:link` **wajib** dijalankan. Tanpa symlink tersebut semua
foto yang tersimpan di disk `public` akan mengembalikan 404, sehingga halaman
tampil tanpa gambar.

## Cara foto ditangani

Seluruh berkas unggahan (foto destinasi, foto paket, avatar, bukti pembayaran)
disimpan pada disk `public` → `storage/app/public`, dan dibaca browser lewat
`/storage/...` melalui symlink `public/storage`.

- `FILESYSTEM_DISK` diatur ke `public` di `.env.example`, dan disk `public`
  memakai URL relatif `/storage` (lihat `config/filesystems.php`) sehingga tetap
  benar walau dijalankan di port atau domain berbeda.
- Path tersimpan di database (`destinations.thumbnail`, `package_images.path`)
  diubah menjadi URL lewat `App\Support\Media::url()`.
- Jika sebuah destinasi atau paket belum punya foto, view memakai
  `public/images/placeholder.svg`, bukan URL kosong.

### Foto contoh

Foto asli beresolusi besar ada di `public/images`. Karena ukurannya mencapai
12 MB, foto tersebut tidak dipakai langsung oleh halaman web. Seeder
menyalinnya ke disk `public` dalam ukuran web (lebar maksimum 1600 px untuk
kartu, 1920 px untuk hero) sehingga totalnya turun menjadi sekitar 3 MB:

```bash
php artisan demo:images          # lewati berkas yang sudah ada
php artisan demo:images --force  # timpa semua foto contoh
```

Daftar sumber, nama berkas tujuan, dan lebar maksimumnya ada di
`app/Support/DemoGallery.php`. Seeder `DestinationSeeder` dan
`TourPackageSeeder` memakai daftar yang sama untuk mengisi kolom gambar,
dan keduanya tidak menimpa foto yang sudah diunggah admin.

## Akun hasil seeder

Semua akun hasil seeder memakai kata sandi `SEED_PASSWORD` (default `password`).
Di luar production, menjalankan ulang `UserSeeder` akan mereset akun demo ke kata sandi tersebut.
Password admin production yang sudah ada tidak diubah saat seeder dijalankan ulang.

| Email                | Peran          |
| -------------------- | -------------- |
| admin@gmail.com      | Admin          |
| tourguide@gmail.com  | Tour Guide     |
| manager@gmail.com    | Manager        |
| user@example.com     | User/Customer  |
| rina@example.com     | User/Customer  |

Akun contoh hanya dibuat di luar production; di production hanya akun admin. Data role
`agent` dan `vendor` yang sudah ada akan diperbarui menjadi `tourguide` dan `manager`
oleh migrasi role.

## Perintah berguna

```bash
php artisan test              # jalankan pengujian
php artisan optimize:clear    # bersihkan cache config, route, dan view
php artisan route:list        # daftar seluruh route
```

## Lisensi

Dirilis di bawah [lisensi MIT](https://opensource.org/licenses/MIT).
