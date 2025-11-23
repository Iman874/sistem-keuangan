# Sistem Manajemen Keuangan Monoframe

Sistem ini digunakan untuk manajemen keuangan di Monoframe, dengan fitur untuk owner dan kasir.

## Fitur Utama

### Owner

-   Dashboard dengan ringkasan keuangan
-   Manajemen user (tambah/edit/hapus kasir)
-   Pengelolaan modal
-   Melihat laporan pemasukan dan pengeluaran dari semua kasir
-   Laporan keuangan terpadu

### Kasir

-   Dashboard khusus kasir
-   Pencatatan pemasukan dan pengeluaran
-   Laporan pemasukan dan pengeluaran per kasir
-   Reset password sederhana

## Fitur Reset Password Sederhana

Sistem menyediakan fitur reset password sederhana khusus untuk akun kasir tanpa perlu verifikasi email atau OTP.

Alur reset password:

1. Kasir mengklik "Lupa Password" di halaman login
2. Memasukkan alamat email
3. Sistem memverifikasi email tersebut terdaftar sebagai akun kasir
4. Jika valid, kasir langsung diarahkan ke halaman reset password
5. Kasir memasukkan password baru dan konfirmasi password
6. Password berhasil diperbarui dan kasir dapat login dengan password baru

Fitur ini memberikan kemudahan bagi kasir yang lupa password tanpa perlu proses verifikasi email yang rumit.

# Panduan Pengeluaran Kasir

## Kategori Pengeluaran

Sistem mendukung dua jenis pengeluaran:

1. **Pengeluaran Harian**: Pengeluaran rutin sehari-hari
2. **Pengeluaran Bulanan**: Pengeluaran yang dilakukan per bulan

Setiap jenis pengeluaran memiliki kategori-kategori tertentu yang sudah disediakan, tetapi Anda juga dapat menambahkan kategori baru sesuai kebutuhan.

### Kategori Default

**Kategori Pengeluaran Harian:**

-   ATK (Alat Tulis Kantor)
-   Transportasi
-   Konsumsi
-   Kebersihan
-   Lain-lain

**Kategori Pengeluaran Bulanan:**

-   Listrik
-   Air
-   Internet
-   Sewa
-   Gaji
-   Wifi
-   Maintenance

## Cara Menambahkan Pengeluaran

1. Pilih jenis pengeluaran (Harian/Bulanan)
2. Pilih kategori pengeluaran dari dropdown yang sesuai
3. Isi deskripsi pengeluaran untuk detail lebih lanjut
4. Masukkan jumlah pengeluaran
5. Upload bukti pengeluaran (jika ada)
6. Klik "Simpan"

## Menambahkan Kategori Baru

Jika kategori yang Anda butuhkan belum tersedia:

1. Klik tombol "+" di samping dropdown kategori
2. Isi nama kategori baru
3. Pilih jenis kategori (Harian/Bulanan)
4. Klik "Simpan"

Kategori baru akan langsung tersedia untuk dipilih saat menambahkan pengeluaran berikutnya.

## Catatan Penting

-   Pastikan memilih kategori yang sesuai dengan jenis pengeluaran
-   Deskripsi pengeluaran sebaiknya spesifik untuk memudahkan pencatatan
-   Setiap pengeluaran akan dicatat dengan waktu otomatis
-   Pengeluaran hanya dapat dilihat oleh kasir yang memasukkan data tersebut dan owner
