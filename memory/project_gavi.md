---
name: GAVI Dashboard Project
description: Project context for GAVI Dashboard - CI4 HMVC web app for Kanwil Kemenkumham DIY
type: project
---

GAVI Dashboard adalah antarmuka web admin untuk sistem multi-agent AI WhatsApp milik Kanwil Kemenkumham DIY.

**Why:** Digunakan untuk AI Agent Competition 2026. Dashboard untuk monitoring, manajemen tiket, knowledge base, dan konfigurasi sistem AI agent.

**Tech Stack:**
- Framework: CodeIgniter 4.3.x (kompatibel PHP 8.0.25 XAMPP yang terinstall)
- Arsitektur: HMVC (folder app/Modules/)
- UI: AdminLTE 3.2.0 via CDN (Bootstrap 4)
- Database: MySQL lokal - database `gavi_dashboard`
- Database RAG: Supabase pgvector via REST API
- Auth: GD CAPTCHA (built-in PHP GD)

**Database:** File schema ada di `db/gavi_database.sql` — 9 tabel: divisions, service_categories, staff, customers, session, chat_logs, tickets, knowledge_base, settings. 361 knowledge base entries sudah ada.

**Login default:** admin@kanwil-diy.go.id / password123

**Modul:** Auth, Dashboard (admin only), Tickets, Knowledge, Staff (admin only), Settings (admin only), API (n8n integration)

**How to apply:** Selalu gunakan env() CI4, Query Builder, esc() untuk XSS protection, $this->response->setJSON() untuk API response.
