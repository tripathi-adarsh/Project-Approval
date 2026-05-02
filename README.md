# Project Approval Workflow System

A production-ready project approval workflow built with Laravel 12, Bootstrap 5, and MySQL.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12 (PHP 8.2) |
| Frontend | Bootstrap 5 (Blade templates) |
| Database | MySQL |
| Auth | Laravel Breeze |
| Queue | Laravel Database Queue |
| Email | Laravel Mailable + Queue |
| API | REST + Laravel Sanctum |
| Charts | Chart.js (CDN) |

---

## Features

### Authentication & Roles
- Login / Register via Laravel Breeze
- Two roles: `admin` and `user`
- Role middleware protecting all admin routes
- Project Policy (approve/reject gates)

### User Features
- Submit projects with file attachment (PDF, DOC, DOCX, XLS, XLSX, ZIP — max 10MB)
- View own projects with status tracking
- Activity timeline per project

### Admin Features
- View all projects with filters (status, date range, search)
- Approve projects via MySQL stored procedure (`sp_approve_project`)
- Reject projects with mandatory reason (modal)
- Bulk approve / bulk reject
- Audit log viewer

### Dashboard
- Total / Pending / Approved / Rejected counts with percentages
- Progress bars per stat
- Bar chart — submissions over last 7 days (Chart.js)
- Recent projects table

### Email Notifications (Queued)
- On project submission
- On approval
- On rejection (includes reason)

### REST API
- `POST /api/projects` — submit project
- `PATCH /api/projects/{id}/approve` — approve project
- Protected via Laravel Sanctum

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/ProjectApiController.php
│   │   ├── DashboardController.php
│   │   ├── ProjectController.php
│   │   └── AuditLogController.php
│   ├── Middleware/RoleMiddleware.php
│   ├── Requests/
│   │   ├── StoreProjectRequest.php
│   │   └── RejectProjectRequest.php
│   └── Resources/ProjectResource.php
├── Jobs/SendProjectNotification.php
├── Mail/ProjectStatusMail.php
├── Models/
│   ├── User.php
│   ├── Project.php
│   ├── Approval.php
│   └── AuditLog.php
├── Policies/ProjectPolicy.php
├── Providers/AppServiceProvider.php
└── Services/ProjectService.php

database/
├── migrations/          (7 migrations including stored procedure)
└── seeders/DatabaseSeeder.php

resources/views/
├── layouts/app.blade.php
├── dashboard.blade.php
├── projects/            (index, create, show)
├── admin/
│   ├── projects/index.blade.php
│   └── audit-logs/index.blade.php
├── auth/                (login, register)
└── emails/project-status.blade.php

routes/
├── web.php
└── api.php
```

---

## Local Setup (XAMPP)

### Requirements
- PHP 8.2+
- Composer 2+
- MySQL (XAMPP)
- Node.js (optional, assets use CDN)

### Steps

**1. Clone / place project in htdocs**
```bash
cd D:\xampp\htdocs
```

**2. Install dependencies**
```bash
composer install
```

**3. Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

Update `.env`:
```env
APP_URL=http://localhost/approval-system/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=approval_system
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
MAIL_MAILER=log
```

**4. Create database**

Open phpMyAdmin or run:
```sql
CREATE DATABASE approval_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**5. Run migrations and seed**
```bash
php artisan migrate --force
php artisan db:seed --force
```

**6. Create storage symlink**
```bash
php artisan storage:link
```

**7. Start the server**
```bash
php artisan serve
```

App runs at: `http://127.0.0.1:8000`

**8. Start queue worker** (separate terminal, for emails)
```bash
php artisan queue:work
```

---

## Demo Accounts

| Role | Email | Password |
|---|---|---|
| Admin | admin@example.com | password |
| User | user@example.com | password |

---

## Stored Procedure

Approval uses a MySQL stored procedure for atomicity:

```sql
CALL sp_approve_project(project_id, admin_id, @result);
SELECT @result; -- 1 = success, 0 = already processed
```

The procedure:
1. Checks project is still `pending`
2. Updates status to `approved`
3. Inserts into `approvals` table
4. Inserts into `audit_logs` table

---

## API Usage

**Submit a project**
```http
POST /api/projects
Authorization: Bearer {token}
Content-Type: multipart/form-data

title=My Project&description=Details here&file=@doc.pdf
```

**Approve a project (admin)**
```http
PATCH /api/projects/{id}/approve
Authorization: Bearer {token}
```

---

## Routes Overview

```
GET    /dashboard
GET    /projects
GET    /projects/create
POST   /projects
GET    /projects/{id}

GET    /admin/projects
PATCH  /admin/projects/{id}/approve
PATCH  /admin/projects/{id}/reject
POST   /admin/projects/bulk
GET    /admin/audit-logs

POST   /api/projects
PATCH  /api/projects/{id}/approve
```

---

## License

MIT
