# Admin Security Implementation Plan for School Management System

This document outlines the detailed security strategy for the Admin module, focusing on Role-Based Access Control (RBAC), Audit Trails, IDOR protection, and Data Privacy.

## 1. Database Schema Enhancements

To support dynamic RBAC and auditing, the following database schema updates are required.

### 1.1. Permissions Table (`permissions`)
Stores all available actions in the system.
```sql
CREATE TABLE IF NOT EXISTS `permissions` (
    `permission_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,  -- e.g., 'student_view', 'payment_approve'
    `description` VARCHAR(100),
    `module` VARCHAR(50) NOT NULL        -- e.g., 'Student', 'Finance', 'System'
);
```

### 1.2. Role-Permissions Mapping (`role_permissions`)
Links roles to specific permissions.
```sql
CREATE TABLE IF NOT EXISTS `role_permissions` (
    `role_id` INT NOT NULL,
    `permission_id` INT NOT NULL,
    PRIMARY KEY (`role_id`, `permission_id`),
    FOREIGN KEY (`role_id`) REFERENCES `roles_config`(`role_id`) ON DELETE CASCADE,
    FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`permission_id`) ON DELETE CASCADE
);
```
*(Note: Ensure `roles_config` has a primary key `role_id`)*

### 1.3. Audit Logs (`audit_logs`)
Records sensitive actions for security review.
```sql
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `log_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,                -- User who performed the action
    `action_type` VARCHAR(50) NOT NULL,    -- e.g., 'UPDATE', 'DELETE', 'LOGIN'
    `module` VARCHAR(50) NOT NULL,         -- e.g., 'Student_Module'
    `record_id` INT NULL,                  -- ID of the affected record (e.g., specific student_id)
    `details` TEXT,                        -- JSON or text description of changes (Old vs New values)
    `ip_address` VARCHAR(45) NOT NULL,
    `timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

---

## 2. Role-Based Access Control (RBAC) Implementation

### 2.1. Logic & Helper Functions
Create a central permission checker (e.g., in `auth_helper.php`) to be included in all admin pages.

```php
function hasPermission($pdo, $role, $permission_name) {
    if ($role === 'superadmin') return true; // Super Admin has all access
    
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM role_permissions rp
        JOIN permissions p ON rp.permission_id = p.permission_id
        JOIN roles_config rc ON rp.role_id = rc.role_id
        WHERE rc.role_name = ? AND p.name = ?
    ");
    $stmt->execute([$role, $permission_name]);
    return $stmt->fetchColumn() > 0;
}
```

### 2.2. Roles-Permissions.php (Implementation Plan)
**Objective**: Transform the static UI into a functional matrix.
1.  **Read Mode**:
    *   Fetch all roles from `roles_config`.
    *   Fetch all permissions from `permissions`, grouped by `module`.
    *   Fetch current mappings from `role_permissions` to pre-check boxes.
2.  **Write Mode (Save Changes)**:
    *   On "Save", clear existing permissions for the Role ID.
    *   Insert new entries into `role_permissions` based on checked boxes.
    *   *Restriction*: Only `superadmin` can edit the "Admin" role permissions.

### 2.3. Admin-Users.php (Implementation Plan)
**Objective**: Assign roles and restrict creation.
1.  **Role Assignment**:
    *   Dropdown for "System Role" populates dynamically from `roles_config`.
2.  **Restrictions**:
    *   **Logic**: `if ($_SESSION['role'] !== 'superadmin') { restrict_access_to_admin_management(); }`
    *   Only `superadmin` can create/delete other admins.
    *   Regular `admin` can only access modules granted to them (view-only on this page or hidden entirely).

---

## 3. Audit Trail System

### 3.1. Logger Function
```php
function logAction($pdo, $user_id, $action, $module, $record_id, $details) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action_type, module, record_id, details, ip_address) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$user_id, $action, $module, $record_id, $details, $ip]);
}
```

### 3.2. Integration Points
*   **Student Editing**: When a student profile is updated, log the changed fields (e.g., "Changed Last Name from Smith to Doe").
*   **Payment Approval**: Log who approved the payment and amount.
*   **User Management**: Log creation/deletion of admin accounts.

---

## 4. IDOR (Insecure Direct Object References) & URL Hijacking

**Problem**: A restricted admin changes `id=5` to `id=6` in the URL to access unauthorized records.

**Solution**:
1.  **Session & Permission Validation**:
    At the top of *every* `submodule/*.php` file:
    ```php
    // Example for Student Edit Page
    require_permission('student_edit'); 
    ```
    Depending on setup, if an admin is restricted to a specific "Department" or "Campus", validate that the `$student_id` belongs to that scope before loading data.

2.  **Scope Enforcement**:
    *   If the admin is "Finance Limited", accessing `/Admin/modules/GradeManagement.php` should immediately redirect to `403 Forbidden`.

---

## 5. Data Privacy & Masking

**Objective**: Protect PII (Personally Identifiable Information) on screen.

### 5.1. Masking Helpers
```php
function maskEmail($email) {
    // exp***@gmail.com
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return $email;
    $parts = explode('@', $email);
    $name = implode('@', array_slice($parts, 0, count($parts)-1));
    $len = strlen($name);
    return substr($name, 0, 3) . str_repeat('*', max(0, $len - 3)) . "@" . end($parts);
}

function maskPhone($phone) {
    // 09******123
    return substr($phone, 0, 2) . str_repeat('*', 6) . substr($phone, -3);
}
```

### 5.2. View Implementation
*   Apply these functions in the `<td>` tags of list views (e.g., `Student-List.php`).
*   **"Click to Reveal"**: Implement a button that fetches the unmasked data via AJAX. This allows logging the "View" action in the `audit_logs`, adding another layer of accountability.

---

## Summary of Next Steps
1.  Run SQL scripts to create new tables.
2.  Populate `permissions` table with initial system modules.
3.  Update `Roles-Permissions.php` to include the `permissions` table logic.
4.  Implement `hasPermission()` middleware.
