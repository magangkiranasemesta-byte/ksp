# Maintenance X — Laravel Migration

Project ini adalah migrasi **Express + MySQL + React/Vite** menjadi **Laravel + Blade + MySQL**. React dan Express tidak lagi diperlukan.

## Fitur
- Login / register berbasis Laravel session.
- Role: ADMIN, ENGINEER, SUPERVISOR, MANAGER.
- Dashboard statistik dan maintenance trend.
- Equipment CRUD untuk ADMIN.
- Request maintenance dengan pagination 7 data/halaman.
- Alur approval: ENGINEER → SUPERVISOR → MANAGER → APPROVED.
- Reject dari Supervisor/Manager.
- Engineer: APPROVED → IN_PROGRESS → COMPLETED.
- Approval history.
- Maintenance history.
- Tampilan responsive dengan CSS vanilla.

## Instalasi Windows
1. Pastikan PHP 8.2+, Composer, dan MySQL sudah terpasang.
2. Ekstrak folder project.
3. Buka terminal di folder project.
4. Jalankan `composer install`.
5. Salin `.env.example` menjadi `.env`.
6. Pastikan `.env` menggunakan database lama:
   - DB_DATABASE=equipment
   - DB_USERNAME=root
   - DB_PASSWORD=
7. Jalankan `php artisan key:generate`.
8. Jalankan `php artisan migrate`.
9. Jalankan `php artisan db:seed`.
10. Jalankan `php artisan serve`.
11. Buka http://127.0.0.1:8000.

## Akun demo
- admin / 123456
- engineer / 123456
- supervisor / 123456
- manager / 123456

## Catatan database lama
Migration dibuat agar dapat membuat tabel yang dipakai sistem bila belum ada. Jika database lama sudah memiliki tabel tersebut, migration tidak menghapus data. **Backup database terlebih dahulu** sebelum migrate.

Jika struktur tabel lama berbeda jauh, sesuaikan migration/model dengan struktur database yang sebenarnya.
