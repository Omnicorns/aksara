# Tema Aksara

Tema Moodle 5.1 turunan Boost untuk E-Learning Aksara.

- Latar hangat, judul serif (Source Serif 4), teks IBM Plex Sans. Font disimpan di folder `fonts`, jadi tidak bergantung pada Google Fonts.
- Halaman login dua kolom: panel merek di kiri, form di kanan.
- Banner berwarna di Dashboard, My courses, halaman depan, dan halaman kursus: sapaan sesuai jam, tanggal, jumlah kursus/mahasiswa, dan gambar kursus sebagai latar jika ada. Bisa dimatikan di pengaturan tema.
- Halaman depan bergaya landing page: hero dengan foto dan tombol, angka statistik (pengguna, mata kuliah, program studi), empat kotak fitur, dan daftar kursus berbentuk kartu.
- Footer situs berisi teks tentang, kontak, dan media sosial.
- Dashboard, My courses, halaman kursus, tabel, form dan footer ditata ulang lewat SCSS di `scss/aksara/`.

## Pemasangan

1. Folder ini harus berada di `public/theme/aksara`.
2. Buka **Site administration → Notifications** untuk memasang plugin.
3. Pilih tema di **Site administration → Appearance → Themes → Aksara → Use theme**.

## Pengaturan

**Site administration → Appearance → Themes → Aksara**

- **General**: warna utama, warna aksen, dan banner judul (aktif/nonaktif).
- **Front page**: judul, teks, foto hero, angka statistik, dan isi empat kotak fitur.
- **Footer**: teks tentang, email, telepon, alamat, dan tautan media sosial.
- **Login page**: kalimat di bawah nama situs dan foto opsional untuk panel kiri.
- **Advanced**: Raw initial SCSS dan Raw SCSS untuk penyesuaian tambahan.

Logo diatur dari **Appearance → Logos**, sama seperti tema lain.

## Mengubah tampilan

| File | Isi |
| --- | --- |
| `_variables.scss` | Warna, font, radius (dipasang sebelum Bootstrap) |
| `_navbar.scss` | Bar atas dan menu |
| `_drawers.scss` | Course index dan blok samping |
| `_header.scss` | Judul halaman, breadcrumb, tab |
| `_banner.scss` | Banner di atas Dashboard, My courses, halaman depan, kursus |
| `_components.scss` | Tombol, kartu, badge, alert, modal |
| `_forms.scss` | Input dan form Moodle |
| `_dashboard.scss` | Kartu kursus, dashboard, halaman depan |
| `_course.scss` | Section dan aktivitas di halaman kursus |
| `_tables.scss` | Tabel, peserta, nilai |
| `_people.scss` | Avatar, edit peran, pilihan autocomplete, badge notifikasi |
| `_frontpage.scss` | Hero halaman depan, kotak fitur, kartu kursus, footer situs |
| `_login.scss` | Halaman login |
| `_footer.scss` | Footer |

Setelah mengubah SCSS, kosongkan cache: **Site administration → Development → Purge caches**.
