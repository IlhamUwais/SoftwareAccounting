# Catatan Temuan Review — FPM Tax App

Dokumen ini berisi hasil investigasi teknis terhadap project `fpm-tax-app-fresh`
(hasil `composer install` + `npm install` yang sudah dilakukan Ilham). Semua
temuan di bawah ini **diverifikasi langsung** dengan cara membaca source code
package yang benar-benar ter-install di `vendor/`, mencoba menjalankan
`php artisan` di sandbox, dan menjalankan `php -l` (syntax check) ke seluruh
file PHP — bukan dugaan/asumsi. Bagian yang belum sempat diverifikasi
ditandai jelas di bagian akhir.

## Metodologi & Keterbatasan Pengujian

- Sandbox reviewer awalnya hanya punya PHP 8.3.6. Project ini ternyata
  **butuh PHP >= 8.4.1** (lihat temuan #5) — jadi `php artisan` tidak bisa
  benar-benar di-boot penuh (migrate, route:list, tinker) di sandbox reviewer.
- Untuk tetap bisa memverifikasi sebanyak mungkin, reviewer membuat **salinan
  sementara** project ini (bukan file asli Ilham), lalu di salinan itu:
  menginstall PostgreSQL lokal, menambal 4 bug kritis di bawah, dan mem-bypass
  platform check — untuk membuktikan bug-bug tersebut benar nyata (bukan
  salah duga) dengan melihat pesan error aslinya.
- Begitu PHP 8.4 dibutuhkan untuk parsing source Symfony (dependency
  Laravel), reviewer **tidak bisa lagi lanjut eksekusi penuh** (migrate ke
  Postgres asli, render Blade, dsb) karena source Symfony 8 memakai syntax
  PHP 8.4-only (property hooks) yang secara harfiah tidak bisa di-parse PHP
  8.3 — bukan cuma "tidak disarankan", tapi benar-benar gagal parse.
- Semua temuan di bawah **valid terlepas dari isu PHP 8.4** — ditemukan lewat
  pembacaan source code langsung, bukan lewat run-time yang gagal.

---

## 🔴 BUG KRITIS — Aplikasi tidak bisa boot sama sekali

### 1. Folder `config/` kehilangan hampir semua file default Laravel
**Status: dikonfirmasi 100%** — `ls config/` di project Ilham hanya
menunjukkan `filesystems.php`. File wajib berikut **hilang total**: `app.php`,
`auth.php`, `cache.php`, `database.php`, `logging.php`, `mail.php`,
`queue.php`, `services.php`, `session.php`, `view.php`, dll.

**Konteks:** ini kemungkinan besar terjadi saat proses "salin file dari paket
kode ke project Laravel fresh" (instruksi di README awal) — folder `config`
tujuan kemungkinan ketimpa/dihapus seluruhnya alih-alih hanya file
`filesystems.php` yang ditimpa.

**Dampak:** Laravel **tidak bisa boot sama sekali** tanpa file-file ini —
ini bug paling fatal, harus diperbaiki PALING PERTAMA sebelum bug lain bisa
diuji.

**Cara perbaiki** (perintah `config:publish` bawaan Laravel 11+ akan
meregenerasi file-file default dari package framework):
```bash
mv config/filesystems.php config/filesystems.php.custom
php artisan config:publish --all
```
Lalu buka `config/filesystems.php` hasil generate baru, dan **manual
tambahkan kembali** disk `r2` (Cloudflare R2) dari isi
`config/filesystems.php.custom` ke dalam array `'disks' => [...]` di file
yang baru, serta pastikan `'default' => env('FILESYSTEM_DISK', 'local')`
tetap ada.

### 2. `routes/console.php` hilang
**Status: dikonfirmasi 100%** — `bootstrap/app.php` mereferensikan file ini
lewat baris:
```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    commands: __DIR__.'/../routes/console.php',
)
```
tapi file `routes/console.php` tidak ada — hanya `routes/web.php` yang ada.
Ini pasti terjadi karena alasan yang sama seperti bug #1 (folder `routes/`
ketimpa seluruhnya, bukan cuma `web.php` yang disalin).

**Dampak:** Laravel gagal boot (mencoba `require` file yang tidak ada).

**Cara perbaiki** — buat file `routes/console.php` dengan isi standar
bawaan Laravel:
```php
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
```

**Rekomendasi tambahan:** setelah dua bug di atas diperbaiki, cek juga
folder-folder lain yang sempat "ditimpa" saat proses penggabungan awal
(`resources/views/` misalnya kehilangan `welcome.blade.php` bawaan — ini
TIDAK masalah karena tidak dipakai, tapi pola ini menunjukkan proses
penggabungan awal kemungkinan melakukan "hapus folder tujuan lalu copy"
alih-alih "merge per file". Ada baik nya di-diff manual sekali lagi folder
`app/`, `database/`, `bootstrap/` terhadap fresh `laravel new` untuk
memastikan tidak ada file bawaan lain yang ikut hilang tanpa disadari.

---

## 🔴 BUG KRITIS — Kesalahan dari kode yang diberikan sebelumnya

### 3. Namespace `spatie/laravel-activitylog` salah di 5 model
**Status: dikonfirmasi 100%** — package yang benar-benar ter-install adalah
versi **5.1.1**, dan versi ini memindahkan lokasi class (breaking change
yang tidak diketahui saat kode awal ditulis, karena ditulis dengan asumsi
versi 4.x).

Kode yang salah (pola lama, v4):
```php
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
```

Namespace yang benar untuk v5.1.1 (dikonfirmasi dengan membaca langsung isi
file di `vendor/spatie/laravel-activitylog/src/`):
```php
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
```

**File yang TERDAMPAK (masih pakai namespace salah):**
- `app/Models/Purchase.php`
- `app/Models/SptDocument.php`
- `app/Models/SalesEntry.php`
- `app/Models/Supplier.php`
- `app/Models/MasterItem.php`

**File yang SUDAH BENAR** (kemungkinan sempat diperbaiki manual oleh Ilham
sebelumnya, tapi belum diterapkan ke 5 file di atas):
- `app/Models/Customer.php`

**Dampak:** fatal error "Class not found" setiap kali salah satu dari 5
model di atas dipakai (dibuat/di-update/dibaca) — karena trait
`LogsActivity` tidak ketemu class-nya.

**Cara perbaiki:** di kelima file di atas, ganti kedua baris `use` sesuai
namespace yang benar. Method (`getActivitylogOptions()`,
`LogOptions::defaults()`, `->logOnly()`, `->logOnlyDirty()`) **tidak
berubah**, jadi cukup ganti baris `use`-nya saja, tidak perlu ubah isi
method.

### 4. Perintah `vendor:publish` untuk migration activity_log — tag salah
**Status: dikonfirmasi 100%** — instruksi sebelumnya memberi perintah:
```bash
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
```

Setelah membaca source code `spatie/laravel-package-tools` (dependency dari
`laravel-activitylog` v5) langsung, tag yang benar-benar terdaftar adalah:
```
laravel-activitylog-migrations
```
(pola penamaan tag mengikuti `{shortName}-migrations`, dan `shortName`
package ini adalah `laravel-activitylog`, bukan `activitylog`).

**Bukti:** folder `database/migrations/` project Ilham memang tidak berisi
file migration untuk tabel `activity_log` sama sekali — konsisten dengan
tag yang salah sehingga publish gagal menemukan apapun untuk dipublish.

**Dampak:** tabel `activity_log` tidak akan pernah ter-migrate, sehingga
begitu bug #3 di atas juga sudah diperbaiki, fitur audit log akan tetap
gagal dengan error database `relation "activity_log" does not exist`.

**Cara perbaiki:**
```bash
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="laravel-activitylog-migrations"
```

---

## 🟡 PERLU DIPASTIKAN — Persyaratan environment

### 5. PHP wajib versi 8.4.1 ke atas
**Status: dikonfirmasi 100%, dengan bukti nyata** — `composer.lock` project
ini me-resolve `laravel/framework` ke versi **13.33.0**, yang menarik
dependency Symfony 8.x. Reviewer mencoba menjalankan project ini di sandbox
dengan PHP 8.3.6 dan mendapat:
```
Composer detected issues in your platform:
Your Composer dependencies require a PHP version ">= 8.4.1"
```
Reviewer sempat mem-bypass pengecekan itu untuk memastikan ini bukan
sekadar peringatan konservatif — hasilnya PHP 8.3 benar-benar **gagal
parse** file `vendor/symfony/http-foundation/Request.php` karena
memakai syntax PHP 8.4-only (**property hooks**, contoh:
`public ParameterBag $attributes { set { ... } }`). Ini bukan bug yang bisa
ditambal — solusinya cuma satu: pastikan PHP di environment (lokal maupun
production) adalah **8.4.1 atau lebih baru**.

**Yang perlu dilakukan:** jalankan `php -v` di environment yang akan
dipakai menjalankan project ini. Kalau masih di bawah 8.4.1, project ini
tidak akan bisa jalan sampai PHP di-upgrade.

---

## 🟢 Temuan terkait Livewire v4 (lompatan versi tak terduga)

Kode awal ditulis dengan asumsi Livewire v3, tapi versi yang benar-benar
ter-install adalah **v4.4.6** — versi major yang jauh lebih baru dari
dugaan awal. Setelah membaca source code v4 dan dokumentasi upgrade
resminya, berikut status tiap area yang berpotensi jadi breaking change:

| Area | Status | Detail |
|---|---|---|
| Computed property gaya lama (`getCardsProperty()` diakses via `$this->cards`) — dipakai di `Dashboard/Index.php` | ✅ **Aman** | v4 tetap punya `SupportLegacyComputedPropertySyntax.php` khusus untuk backward-compat pola ini |
| `$this->authorize()` di dalam Livewire component | ✅ **Aman** | `Livewire\Component` masih pakai trait `AuthorizesRequests` bawaan Laravel |
| Directive `@livewireStyles` / `@livewireScripts` di `layouts/app.blade.php` & `layouts/guest.blade.php` | ✅ **Aman** | Masih terdaftar persis sama di v4 (`FrontendAssets.php`) |
| `#[Layout('layouts.app')]` attribute di tiap komponen | ✅ **Aman** | v4 mengganti *default* config layout ke format namespace `layouts::app`, TAPI karena semua komponen di project ini selalu eksplisit set `#[Layout('layouts.app')]` sendiri (dot notation biasa), perubahan default config ini tidak berpengaruh |
| `Route::get('/dashboard', Dashboard::class)` (component langsung jadi route action, dipakai di seluruh `routes/web.php`) | ⚠️ **Kemungkinan besar aman, tapi belum tervalidasi jalan nyata** | v4 menambahkan cara baru `Route::livewire($uri, Component::class)` sebagai "preferred method". Reviewer cek: `Livewire\Component` (lewat trait `HandlesPageComponents`) **masih punya method `__invoke()`**, dan `Route::get($uri, Class::class)` untuk class ber-`__invoke()` adalah fitur native Laravel (bukan magic khusus Livewire) — jadi pola lama **seharusnya tetap berfungsi**. Tapi ini belum bisa dibuktikan jalan nyata karena keterbatasan PHP 8.4 di sandbox (lihat temuan #5). **Perlu diuji langsung** setelah PHP 8.4 tersedia. |
| `wire:model.blur` / `wire:model.change` (v4.1 mengubah perilaku modifier ini) | ✅ **Tidak relevan** | Sudah dicek — project ini cuma pakai `wire:model` polos, `wire:model.live`, dan `wire:model.live.debounce.300ms`. Tidak ada satupun `.blur`/`.change` di codebase, jadi perubahan perilaku ini tidak berdampak |
| `config/livewire.php` tidak pernah di-publish | ✅ **Tidak masalah** | Package tetap auto-merge config default lewat `mergeConfigFrom()` di `LivewireServiceProvider`, jadi tidak wajib dipublish kecuali memang mau override setting tertentu |

**Kesimpulan Livewire:** kemungkinan besar tidak ada breaking change yang
benar-benar merusak kode yang sudah ditulis, TAPI baris `Route::get(...,
Component::class)` di poin ⚠️ di atas **wajib diuji langsung begitu PHP 8.4
tersedia**, karena ini satu-satunya area yang belum bisa dibuktikan 100%
jalan tanpa eksekusi nyata.

---

## ✅ Yang sudah diverifikasi AMAN (tidak ada masalah)

- **Syntax check**: seluruh 64 file PHP di `app/`, `database/`, `routes/`,
  `bootstrap/` lolos `php -l` tanpa error sama sekali.
- **Konsistensi referensi class**: semua pemakaian `App\Models\...`,
  `App\Policies\...`, `App\Livewire\...`, dll di seluruh codebase merujuk ke
  file yang benar-benar ada (tidak ada typo path/namespace).
- **API `smalot/pdfparser`**: method `Parser::parseFile()` dan
  `Document::getText()` yang dipakai di `FpmPdfParser.php` cocok persis
  dengan signature package versi 2.12.5 yang ter-install.
- **`Illuminate\Foundation\Support\Providers\AuthServiceProvider`** (base
  class yang di-extend `app/Providers/AuthServiceProvider.php`) masih ada
  dan valid di Laravel 13.
- **`Middleware::alias()`** di `bootstrap/app.php` — method ini masih ada
  dan valid signature-nya di Laravel 13.
- **Struktur migration**: 13 file migration custom + `sessions`/`cache`/
  `jobs` bawaan Laravel sudah tergabung dengan urutan yang benar, tidak ada
  file default yang salah hilang/duplikat di folder ini (berbeda dengan
  `config/` dan `routes/` yang bermasalah).
- **`config/activitylog.php` tidak pernah di-publish** — dicek, ini AMAN
  (sama seperti `config/livewire.php`, auto-merge dari default package).

---

## ⏸️ BELUM SEMPAT / TIDAK BISA diverifikasi (keterbatasan sandbox)

Karena PHP 8.4 tidak tersedia di sandbox reviewer, hal-hal berikut **belum
bisa dibuktikan langsung** dan sebaiknya jadi prioritas pengujian pertama
begitu lingkungan PHP 8.4 tersedia (baik lokal Ilham maupun server):

1. **`php artisan migrate` yang sebenarnya** terhadap PostgreSQL — apakah
   ke-13 migration custom benar-benar jalan tanpa error SQL (termasuk
   partial unique index PostgreSQL yang dipakai untuk soft-delete
   constraint).
2. **`php artisan route:list`** — untuk memastikan semua route ke-load
   tanpa error, termasuk validasi poin ⚠️ di tabel Livewire di atas.
3. **Render Blade sungguhan** (`php artisan view:cache` atau buka
   halaman login/dashboard di browser) — belum ada satupun blade view yang
   benar-benar di-compile dan dicek hasilnya.
4. **Alur upload FPM end-to-end** — apakah `FpmPdfParser` regex-nya benar-
   benar berhasil mengekstrak data dari PDF asli Coretax lain (baru diuji
   struktur dari SATU contoh PDF).
5. **Queue worker** (`php artisan queue:work`) — apakah job
   `ProcessFpmUpload` benar-benar jalan di background dan meng-update
   `import_batches`/`import_files` dengan benar.
6. Kemungkinan ada breaking change Livewire v4 lain yang belum ketemu,
   karena reviewer tidak sempat membandingkan changelog v4 secara
   menyeluruh baris-per-baris terhadap seluruh pemakaian API Livewire di
   codebase ini (baru area-area yang paling berisiko yang dicek).

---

## 📋 Fitur dari requirement yang MASIH BELUM ADA (bukan bug, tapi belum dikerjakan)

Ini bukan error, tapi bagian requirement yang sudah disepakati di awal
diskusi namun belum ada implementasinya sama sekali:

1. **Halaman ganti password** — untuk Customer (ganti password sendiri)
   maupun SuperAdmin (set/reset password customer lain). Kolom database
   sudah siap, form/UI belum ada.
2. **Halaman Profile Customer** (lihat nama perusahaan & username sendiri)
   — belum dibuat.
3. **Edit data Customer** oleh SuperAdmin (ubah nama perusahaan / username)
   — baru ada "create" dan "toggle status aktif/nonaktif", belum ada form
   edit.
4. **Halaman riwayat Import Batch** — model & proses background sudah
   jalan, tapi belum ada halaman untuk SuperAdmin melihat daftar batch
   upload yang sudah lewat (saat ini hasil batch cuma terlihat selama masih
   di halaman upload yang sama, hilang begitu pindah halaman).
5. **Tombol restore untuk Supplier & Master Item** — soft delete di
   modelnya sudah ada, tapi tombol "lihat yang terhapus + restore" baru
   dibuat untuk modul Purchase saja.
6. **Halaman/viewer Audit Log** — data audit log sudah otomatis tercatat
   (setelah bug #3 & #4 di atas diperbaiki), tapi **belum ada UI untuk
   SuperAdmin benar-benar membuka/browse riwayat perubahan itu**. Data ada,
   tampilan belum ada. Ini requirement eksplisit yang penting karena
   aplikasi ini menyimpan data akuntansi/pajak.
7. **Tombol download PDF FPM asli** di halaman daftar pembelian — route
   dan otorisasinya (`FileAccessController::purchaseDocument`) sudah
   dibuat, tapi tombolnya belum dipasang di `purchase-list.blade.php`.
8. **Validasi MIME-type ganda** untuk upload SPT — baru validasi ekstensi
   via aturan `mimes:` Laravel (yang sebenarnya sudah otomatis cross-check
   MIME juga secara internal), tapi belum ada pesan error khusus yang jelas
   kalau MIME tidak cocok dengan ekstensi.
9. **Automated test** (PHPUnit/Pest) — belum ada sama sekali, hanya file
   contoh bawaan Laravel (`ExampleTest.php`) yang belum diganti.
10. **Chart.js** dimuat lewat CDN eksternal (`jsdelivr.net`) di
    `layouts/app.blade.php`, bukan lewat npm/vite bundling — perlu
    dipertimbangkan kalau production butuh 100% self-contained tanpa
    dependency ke CDN luar.
11. Tidak ada mekanisme "lupa password" untuk akun SuperAdmin sendiri.

---

## Urutan Perbaikan yang Disarankan

1. **Perbaiki dulu bug #1 dan #2** (`config/` dan `routes/console.php`) —
   tanpa ini, tidak ada satupun perintah `artisan` yang bisa jalan sama
   sekali, jadi ini prasyarat mutlak sebelum menguji apapun yang lain.
2. **Pastikan PHP di environment sungguhan adalah 8.4.1+** (temuan #5) —
   kalau belum, upgrade dulu, karena tanpa ini aplikasi tidak akan pernah
   bisa dijalankan meski semua bug lain sudah diperbaiki.
3. **Perbaiki bug #3** (namespace 5 model) dan **#4** (tag vendor:publish
   yang benar), lalu jalankan migration.
4. **Jalankan `php artisan migrate`, `php artisan route:list`, dan buka
   halaman login di browser** — ini akan langsung mengonfirmasi atau
   membantah kekhawatiran soal `Route::get(..., Component::class)` di
   bagian Livewire v4 di atas, dan kemungkinan memunculkan bug lain yang
   belum ketemu lewat pembacaan source code saja.
5. Setelah aplikasi bisa jalan dan alur inti (login → upload FPM →
   dashboard) terbukti berfungsi, baru lanjutkan mengerjakan daftar fitur
   yang belum ada di atas — prioritaskan **Audit Log viewer** (poin 6)
   karena itu requirement eksplisit yang paling terasa kalau hilang.
