# Startiz Labs — Enterprise Digital Business Solution Platform

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-%5E8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/License-Proprietary-blue?style=for-the-badge)](LICENSE)

## Overview

**Startiz Labs** is an all-in-one enterprise business platform and digital business solutions hub. One place for your complete digital business solution: websites, mobile apps, business software, AI automation, and custom technology for growing businesses.

---

## 🚀 Core Business Solutions & Services

Startiz Labs powers end-to-end digital transformation across 11 key business solution domains:

* **Website Development**: Custom, responsive, high-converting web applications.
* **Mobile App Development**: Native iOS/Android and cross-platform mobile apps.
* **AI Automation**: Custom AI chatbots, automated document processing, workflow engines.
* **CRM Development**: Custom pipeline tracking, client management, quotation engine.
* **E-Commerce Solutions**: Digital storefronts, payment gateways, inventory control.
* **Restaurant Solutions**: Digital QR menus, kitchen display systems, order management.
* **Institute & Education Solutions**: Student management, online exams, digital note repositories.
* **Business Management Software**: ERP/CRM automation, project tracking, staff task allocation.
* **Custom Software Development**: Bespoke web applications built to exact business logic.
* **Business Automation**: Automated quotation approvals, invoice generation, status sync.
* **Cybersecurity & Technology Solutions**: Enterprise security audits, data isolation, role-based security.

---

## 🏗️ Architecture & Technology Stack

* **Backend Framework**: Laravel 12 (PHP ^8.2)
* **Database**: MySQL 8.0 / SQLite (dev)
* **UI & Frontend**: Vite, Blade, Modern Responsive Styling
* **Hosting Compatibility**: Hostinger Shared / VPS Hosting (Standard Apache/Nginx `public` entrypoint)
* **Security & Auth**: Role-Based Access Control (RBAC), Server-side authorization, Eloquent parameterization, Encrypted storage streams
* **Reference Prefix Strategy**: Mandatory `STZ-*` prefix system (`STZ-LEAD`, `STZ-PROJ`, `STZ-QUO`, `STZ-INV`, `STZ-PAY`, `STZ-REC`, `STZ-DOC`, `STZ-TKT`, `STZ-CR`).

### Documentation Links
* 📘 [Functional Requirements Blueprint](docs/requirements.md)
* 🏛️ [System Architecture & Schema Design](docs/architecture.md)

---

## 📂 Project Structure

```
startiz-labs/
├── app/
│   ├── Enums/                 # Application enums & state definitions
│   ├── Http/                  # Controllers, Middleware, Form Requests
│   ├── Models/                # Eloquent ORM models & relationships
│   ├── Policies/              # Access Control Policies (RBAC)
│   ├── Repositories/          # Data access abstraction layer
│   └── Services/              # Domain business logic processing layer
├── bootstrap/                 # Application bootstrap & configuration
├── config/                    # Framework configuration files
├── database/                  # Migrations, Seeders, Factories
├── docs/                      # System Architecture & Requirements documentation
│   ├── architecture.md
│   └── requirements.md
├── public/                    # Web root entrypoint
├── resources/                 # Blade templates, JS assets, CSS styles
├── routes/                    # Application web and console routes
├── storage/                   # Logs, cached templates, user uploads
├── tests/                     # Automated test suites
├── .env.example               # Environment variables configuration template
├── composer.json              # PHP dependencies
├── package.json               # Frontend dependencies & build scripts
└── README.md                  # Project documentation
```

---

## 🛠️ Quickstart Development Guide

### Prerequisites
* PHP >= 8.2 with PDO, OpenSSL, Mbstring, Fileinfo extensions
* Composer >= 2.x
* Node.js & npm (for asset compilation)

### Setup Steps
1. **Clone the repository**:
   ```bash
   git clone https://github.com/DollyQx/startiz-labs.git
   cd startiz-labs
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Configure Environment Variables**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Run Database Migrations & Seeders**:
   ```bash
   php artisan migrate --seed
   ```

5. **Start Local Development Server**:
   ```bash
   php artisan serve
   ```

---

## 🌿 Git Branching & Version Control Guidelines

* `main`: Production-ready branch. Must remain stable at all times.
* `develop`: Integration branch for ongoing development.
* **Commits**: Feature-based, meaningful commit messages adhering to standard conventions (`feat:`, `fix:`, `docs:`, `chore:`).
