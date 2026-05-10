# Database

File `gavi_dashboard.sql` berisi dump lengkap database GAVI, termasuk:

- Skema seluruh tabel
- Data awal (seeding): staff admin, divisi, service categories, settings default

## Import

```bash
mysql -u root -p gavi_dashboard < database/gavi_dashboard.sql
```

## Akun Default Setelah Import

| Email | Password | Role |
|---|---|---|
| `admin@kanwil-di.go.id` | `admin123` | Admin |

> **Segera ganti password setelah login pertama** melalui menu Manajemen → Staff → Detail → Reset Password.

## Tabel Utama

| Tabel | Keterangan |
|---|---|
| `staff` | Akun pengguna dashboard (admin & petugas) |
| `divisions` | Divisi/bidang dalam instansi |
| `service_categories` | Kategori layanan (terhubung ke divisi) |
| `customers` | Data customer/masyarakat yang berinteraksi via WA |
| `session` | Sesi percakapan WhatsApp |
| `chat_logs` | Log seluruh pesan (customer, AI, staff) |
| `tickets` | Tiket eskalasi ke staf manusia |
| `knowledge_base` | Artikel FAQ sebagai sumber pengetahuan AI |
| `settings` | Konfigurasi sistem (WAHA, N8N, Supabase, RAG) |
