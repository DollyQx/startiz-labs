# GM Code Lab — Functional & System Requirements

## 1. System Overview

**GM Code Lab** is a complete, enterprise-grade business platform designed for a high-performance digital solutions agency. The application handles public showcase, client onboarding, requirement gathering, lead management, automated quote generation, project execution, milestones, ticketing, online invoicing, and financial reporting.

---

## 2. Business Scope & Digital Solutions Offered

GM Code Lab delivers tailored digital solutions across diverse business domains:

1. **Web Development**: Custom web applications, enterprise portals, progressive web apps.
2. **App Development**: Native and cross-platform mobile solutions.
3. **Custom Software**: Tailored enterprise management systems and workflows.
4. **LMS & Teaching Platforms**: E-learning environments, course management, video streaming, student tracking.
5. **Online Test / Examination Systems**: Proctored testing, timed exams, automated grading, result analytics.
6. **Notes & Digital Library Platforms**: Digital content delivery, secure PDF streaming, subscription access.
7. **Institute / Education Management Systems**: School/college administration, fee management, attendance, student records.
8. **Hostel Management**: Room allocation, student onboarding, fee records, complaint management.
9. **Mess & Canteen Management**: Meal planning, coupon/card tracking, billing systems.
10. **Hospital / Healthcare Management Systems**: Patient records, appointments, prescription management, billing.
11. **Shop / Retail Systems**: Inventory control, POS, order tracking, sales analytics.
12. **E-Commerce Solutions**: Digital storefronts, payment gateway integration, order fulfillment.
13. **Business Management Software**: ERP/CRM automation, inventory, payroll, workflow automation.
14. **Cybersecurity Services**: Security audits, vulnerability assessments, penetration testing, compliance hardening.

---

## 3. Core Functional Requirements & System Modules

### 3.1 Public Company Website & CMS
- **Homepage & Service Showcase**: Modern dynamic pages for all 14 solution verticals.
- **Industries CMS**: Industry-tailored landing pages showcasing expertise and solution blueprints.
- **Portfolio CMS**: Interactive case studies, live project previews, tech stack tags, and client testimonials.
- **Interactive Solution Estimator**: Client-facing project estimator to calculate dynamic cost ranges based on selected features.

### 3.2 Client Registration & Authentication
- Self-service client signup with email verification and multi-factor capabilities.
- Role-based authentication (Client, Super Admin, Project Manager, Financial Admin, Support Specialist).
- Password reset, session management, and login activity tracking.

### 3.3 Client Portal & Dashboard
- Consolidated dashboard showing active projects, pending quotes, unpaid invoices, open tickets, and recent documents.
- Real-time project progress bar and milestone tracking.
- Direct quick actions for project requirement submission, invoice payment, and support ticket creation.

### 3.4 Project Requirement Submission
- Multi-step structured project intake form.
- Feature checklist selection (e.g., Auth, Payment Gateway, Admin Panel, Mobile App, Analytics).
- File upload for SRS documents, wireframes, and design assets.
- Budget range and target delivery timeline specification.

### 3.5 Lead & CRM Management (Admin & Sales)
- Centralized lead database automatically capturing submissions from website contact forms and requirement estimators.
- Lead pipeline stages: `New` -> `Contacted` -> `Requirements Gathered` -> `Quote Sent` -> `Won` / `Lost`.
- Internal notes, follow-up scheduling, and activity logs.

### 3.6 Quotations & Proposal Engine
- Dynamic quotation builder for project managers to create itemized cost breakdowns.
- Digital proposal delivery to clients via portal and email.
- Client single-click Acceptance / Revision Request workflow.
- Automated conversion of accepted quotes into active projects and initial deposit invoices.

### 3.7 Project Management, Milestones & Tasks
- Project breakdown into structured Milestones and Tasks.
- Status tracking (`Planning`, `In Progress`, `In Review`, `Completed`, `On Hold`).
- Milestone approval workflow with client sign-off.
- Task assignment to internal team members with completion percentages.

### 3.8 Financial Management & Online Payments
- Payment Gateway integration supporting credit/debit cards, UPI, net banking, and international payments.
- Automatic creation of line-item Invoices linked to project milestones or retainers.
- Automated payment receipts and downloadable PDF invoices.
- Payment status tracking (`Draft`, `Sent`, `Partial`, `Paid`, `Overdue`).

### 3.9 Offers & Coupon Management
- Promotional discount and coupon code engine.
- Discount types: Percentage discount or fixed amount reduction.
- Usage constraints: Expiry dates, minimum project value, max redemptions per client.

### 3.10 Document Management System
- Centralized repository for project contracts, scope of work (SOW), design files, and final deliverables.
- Secure, access-controlled download links for clients.

### 3.11 Support & Ticketing System
- Integrated ticketing tool for post-launch maintenance, bug reports, and change requests.
- Ticket priorities (`Low`, `Medium`, `High`, `Urgent`) and status tracking (`Open`, `In Progress`, `Waiting Client`, `Resolved`, `Closed`).
- Threaded conversation style with file attachment support.

### 3.12 Notifications System
- Multi-channel notification delivery (In-App Dashboard Alerts & Email notifications).
- Triggers: New quote available, milestone completion, invoice generation, ticket reply, status change.

### 3.13 Admin Panel & Role-Based Access Control (RBAC)
- Fine-grained permission management for team members.
- Roles: `Super Admin`, `Project Manager`, `Finance Lead`, `Support Specialist`, `Client`.
- User activity logs and security audit trail.

### 3.14 Analytics & Executive Reporting
- Financial metrics: Monthly recurring revenue (MRR), total invoiced vs paid, pending receivables.
- Operational metrics: Lead conversion rate, active projects count, ticket resolution average time.

---

## 4. Technical & Infrastructure Requirements

1. **Framework**: Laravel 12 (PHP 8.2+).
2. **Database**: MySQL 8.0 / MariaDB with strict relational integrity and foreign key constraints.
3. **Hosting Target**: Hostinger Shared Hosting compatibility (Standard Apache/Nginx web root alias `public`).
4. **Security**:
   - CSRF protection across all web endpoints.
   - Prepared SQL statements via Eloquent ORM to prevent SQL injection.
   - XSS sanitization and output escaping.
   - Encrypted passwords (Bcrypt/Argon2id).
   - Strict Authorization Policies (Laravel Gates & Policies).
5. **No Unnecessary Dependencies**: Pure, clean Laravel architecture with standard packages only. No microservices overhead.
