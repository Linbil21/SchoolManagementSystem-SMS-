# 🎓 School Management System - Admin Architecture

This document serves as the technical overview for the School Management System (SMS) admin panel and its database structure.

## 🏛️ Enrollment Modules (`Admin/Modules`)

These modules handle the lifecycle of student admission and academic organization.

### Core Database Tables:
- **`courses`**: Academic programs offered by the institution.
- **`enrollments`**: Stores student applications, personal data, and requirements.
- **`subjects`**: Manages curriculum items (Code, Name, Units).
- **`sections`**: Organization of students into classes with capacity limits.
- **`payments`**: Records of tuition and fee transactions linked to student IDs.
- **`academic_years`**: Manages active/inactive school years for historical context.

### Logic Highights:
- **Enrollment Queue**: Processes incoming freshman and transferee applications.
- **Archiving**: Students who are rejected or graduated are moved to `Enrollment History`.
- **Reporting**: Aggregated statistics dashboard using cross-table joins.

---

## 👥 User & System Management (`Admin/Submodules`)

These modules provide the tools for internal administration and security.

### Core Database Tables:
- **`users`**: Unified table for Admins, Staff, and Students (linked from enrollments).
- **`roles_config` & `permissions`**: Granular access control for different staff types.
- **`departments`**: Institutional organization mapping.
- **`system_logs`**: Tracks sensitive actions within the admin panel for auditing.

### Feature Highights:
- **Account Status**: Real-time monitoring of online/offline status across all user types.
- **Staff Registration**: Secure account creation for Registrars, Cashiers, and Admission officers.
- **RBAC**: Every page implements a strictly enforced Role-Based Access Control check.

---

## 🛠️ Global Configuration
Database connection is centralized in `Database/config.php` using **PDO**. All modules utilize a unified CSS grid system (`admin.css`) and standardized modals for consistent UX.
