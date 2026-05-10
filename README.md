<div align="center">

# GAVI — Generatif Asisten Virtual Instansi

**Sistem chatbot AI berbasis WhatsApp untuk pelayanan publik instansi pemerintah**

[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?logo=php&logoColor=white)](https://php.net)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.6.5-EF4223?logo=codeigniter&logoColor=white)](https://codeigniter.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)](https://mysql.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

</div>

---

## Tentang GAVI

GAVI (*Generatif Asisten Virtual Instansi*) adalah sistem dashboard manajemen chatbot AI untuk pelayanan publik berbasis WhatsApp. Sistem ini dirancang agar instansi pemerintah maupun lembaga publik dapat mengadopsinya secara **gratis** dan **mandiri** untuk meningkatkan kualitas pelayanan kepada masyarakat.

GAVI menghubungkan teknologi AI generatif dengan WhatsApp melalui integrasi **WAHA** (WhatsApp HTTP API) dan **N8N** (workflow automation), sehingga instansi dapat melayani pertanyaan masyarakat secara otomatis 24 jam. Staf dapat memantau, mengambil alih, dan merespons percakapan secara langsung melalui dashboard.

---

## Fitur

### Percakapan AI-Powered
- Tampilan mirip WhatsApp Web untuk memantau seluruh percakapan customer
- AI menjawab otomatis sesuai knowledge base instansi
- Staf dapat **ambil alih** percakapan kapan saja dan membalas langsung
- **Panel template** untuk mempercepat balasan
- Polling real-time setiap 5 detik tanpa reload halaman
- Integrasi kirim pesan balik ke WhatsApp via WAHA API

### Tiket Eskalasi
- Tiket dibuat otomatis ketika AI tidak dapat menangani permintaan
- Prioritas tiket: High / Medium / Low
- Status tiket: Terbuka → Pending → Selesai → Ditutup
- Detail tiket lengkap: riwayat percakapan, profil customer, template balasan
- Assign tiket ke staf, ambil alih, dan resolusi dengan catatan

### Manajemen Customer
- Daftar customer dengan statistik: total sesi, chat, dan tiket
- Detail profil: NIK, instansi, kota, provinsi, alamat
- Riwayat percakapan lengkap per sesi
- Riwayat tiket yang pernah dibuat

### Knowledge Base (RAG)
- Kelola artikel/FAQ sebagai sumber pengetahuan AI
- Sinkronisasi ke Supabase untuk vector search (RAG)
- Kategorisasi per layanan
- Template balasan siap pakai di modul percakapan dan tiket

### Manajemen Staff
- CRUD akun staff dengan role: Admin dan Petugas
- Detail statistik per staf: tiket ditangani, open, resolved
- Toggle aktif/nonaktif, reset password
- Manajemen Divisi dan Layanan (service categories)

### Dashboard & Analytics *(soon)*
- Overview statistik real-time
- Grafik tren tiket dan percakapan

### Pengaturan Sistem
- **General**: nama aplikasi, master AI switch
- **RAG/AI**: confidence threshold, system prompt
- **N8N**: webhook URL + API token + test koneksi
- **WAHA**: endpoint URL, session name, API key + test koneksi
- **Supabase**: project URL + service role key

---

## Teknologi yang Digunakan

| Komponen | Teknologi |
|---|---|
| Backend Framework | [CodeIgniter 4.6.5](https://codeigniter.com) (PHP 8.1+) |
| Database | MySQL 8.0 |
| Frontend UI | [AdminLTE 3.2](https://adminlte.io) + Bootstrap 4 + jQuery |
| WhatsApp API | [WAHA](https://waha.devlike.pro) (WhatsApp HTTP API) |
| Workflow Automation | [N8N](https://n8n.io) |
| Vector Database | [Supabase](https://supabase.com) (pgvector) |
| AI / LLM | Dapat dikonfigurasi via N8N (OpenAI, Gemini, dsb.) |
| Web Server | Apache / Nginx (Laragon direkomendasikan untuk dev) |

---

## Cara Instalasi

### Prasyarat
- PHP 8.1 atau lebih baru (dengan ekstensi: `intl`, `mbstring`, `curl`, `json`, `mysqlnd`)
- MySQL 8.0+
- Composer
- Web server (Apache/Nginx) atau [Laragon](https://laragon.org) untuk Windows

### Langkah Instalasi

**1. Clone repositori**
```bash
git clone https://github.com/USERNAME/gavi.git
cd gavi
```

**2. Install dependensi PHP**
```bash
composer install
```

**3. Buat database**
```sql
CREATE DATABASE gavi_dashboard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**4. Import skema database**
```bash
mysql -u root -p gavi_dashboard < database/gavi_dashboard.sql
```

> File `database/gavi_dashboard.sql` berisi skema tabel dan data awal (seeding).  
> Lihat [`database/README.md`](database/README.md) untuk daftar tabel dan akun default.

**5. Salin file konfigurasi environment**
```bash
cp .env.example .env
```

**6. Edit file `.env`**
```env
CI_ENVIRONMENT = production

database.default.hostname = 127.0.0.1
database.default.database = gavi_dashboard
database.default.username = root
database.default.password = PASSWORD_ANDA
database.default.port     = 3306
database.default.DBDriver = MySQLi
```

**7. Set permission folder writable**
```bash
chmod -R 777 writable/
```

**8. Akses dashboard**

Buka browser ke `http://localhost/gavi/public` atau sesuai konfigurasi virtual host Anda.

**Login default:**
| Email | Password | Role |
|---|---|---|
| `admin@instansi.go.id` | `admin123` | Admin |

> **Segera ganti password default setelah login pertama.**

---

## Cara Konfigurasi

Setelah berhasil login, masuk ke menu **Pengaturan Sistem** untuk mengkonfigurasi integrasi.

### 1. Pengaturan General
- Ubah **Nama Aplikasi** sesuai nama instansi Anda
- Aktifkan/nonaktifkan **Master AI Switch** sesuai kebutuhan

### 2. Integrasi WAHA (WhatsApp)
WAHA adalah server WhatsApp API yang perlu dijalankan secara terpisah.

```bash
# Jalankan WAHA via Docker
docker run -it --rm \
  -p 3000:3000/tcp \
  devlikeapro/waha
```

Kemudian isi di **Pengaturan → WAHA**:
- **Endpoint URL**: `http://localhost:3000`
- **Session Name**: `default` (atau nama session yang Anda buat di WAHA)
- **API Key**: sesuai konfigurasi WAHA Anda

Klik **Test Koneksi** untuk memverifikasi.

### 3. Integrasi N8N (AI Workflow)
N8N digunakan untuk menghubungkan pesan WhatsApp masuk dengan AI (OpenAI, Gemini, dsb.) dan mengirim respons kembali.

```bash
# Jalankan N8N via Docker
docker run -it --rm \
  -p 5678:5678 \
  n8nio/n8n
```

Kemudian isi di **Pengaturan → N8N**:
- **Webhook URL**: URL webhook workflow N8N Anda
- **API Token**: token autentikasi N8N

Klik **Test Koneksi** untuk memverifikasi.

### 4. Integrasi Supabase (RAG / Vector Search)
Supabase digunakan untuk menyimpan embedding knowledge base yang memungkinkan AI menjawab pertanyaan berdasarkan dokumen instansi.

1. Buat project baru di [supabase.com](https://supabase.com)
2. Aktifkan ekstensi `pgvector` di SQL Editor:
   ```sql
   CREATE EXTENSION IF NOT EXISTS vector;
   ```
3. Isi di **Pengaturan → Supabase**:
   - **Project URL**: `https://xxxx.supabase.co`
   - **Service Role Key**: dari menu Project Settings → API

### 5. Pengaturan RAG / AI
- **Confidence Threshold**: batas minimal skor kepercayaan AI sebelum eskalasi ke staf (default: 80%)
- **System Prompt**: instruksi kepribadian dan perilaku AI Anda

---

## Struktur Direktori

```
gavi/
├── app/
│   ├── Config/
│   │   └── Routes.php              # Definisi routing
│   ├── Filters/                    # Auth & API filters
│   ├── Modules/                    # Modul HMVC
│   │   ├── Api/                    # Endpoint API eksternal
│   │   ├── Auth/                   # Login & autentikasi
│   │   ├── Conversations/          # Percakapan AI-Powered
│   │   ├── Customers/              # Manajemen customer
│   │   ├── Dashboard/              # Dashboard & analytics
│   │   ├── Knowledge/              # Knowledge base
│   │   ├── Settings/               # Pengaturan sistem
│   │   ├── Staff/                  # Manajemen staff, divisi, layanan
│   │   └── Tickets/                # Tiket eskalasi
│   └── Views/
│       ├── layouts/                # Layout utama AdminLTE
│       └── partials/               # Komponen (navbar, sidebar)
├── public/                         # Document root web server
├── writable/                       # Cache, logs, sessions
├── .env.example                    # Template konfigurasi
└── composer.json
```

---

## Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE) — bebas digunakan, dimodifikasi, dan didistribusikan untuk keperluan instansi pemerintah maupun swasta.

---

## Kontribusi

Kontribusi sangat disambut! Silakan buka *issue* untuk melaporkan bug atau mengusulkan fitur baru, atau buat *pull request* langsung.

---

<div align="center">
Dibuat untuk meningkatkan pelayanan publik Indonesia
</div>
