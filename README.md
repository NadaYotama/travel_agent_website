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

`php artisan storage:link` **wajib** dijalankan. Gambar contoh halaman utama
sudah disimpan dan dilacak di Git pada `storage/app/public`, sehingga setelah
clone perintah ini membuatnya dapat diakses lewat `/storage/...`.

## Cara foto ditangani

Seluruh berkas unggahan (foto destinasi, foto paket, avatar, bukti pembayaran)
disimpan pada disk `public` → `storage/app/public`, dan dibaca browser lewat
`/storage/...` melalui symlink `public/storage`.

- `FILESYSTEM_DISK` diatur ke `public` di `.env.example`, dan disk `public`
  memakai URL relatif `/storage` (lihat `config/filesystems.php`) sehingga tetap
  benar walau dijalankan di port atau domain berbeda.
- Path tersimpan di database (`destinations.thumbnail`, `package_images.path`)
  diubah menjadi URL lewat `App\Support\Media::url()`.
- Gambar contoh hero, destinasi, dan paket tersedia di
  `storage/app/public/hero.jpg`, `destinations/`, dan `packages/`.
- Seeder menghubungkan gambar contoh ke destinasi dan paket; jalankan
  `php artisan migrate:fresh --seed` untuk mengisi data demo pada database baru.

### Foto contoh

Foto contoh dalam ukuran web disimpan langsung di Git pada
`storage/app/public`; tidak perlu mengunggah atau menyalin foto secara manual.
Pastikan menjalankan `php artisan storage:link` agar browser dapat membacanya.
Daftar nama berkas yang digunakan seeder ada di `app/Support/DemoGallery.php`.

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
