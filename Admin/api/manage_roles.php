<?php
session_start();
require_once '../../Database/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$action = $_GET['action'] ?? '';

if ($action === 'get_role_permissions') {
    $role_id = $_GET['role_id'] ?? null;
    if (!$role_id) {
        echo json_encode(['success' => false, 'message' => 'Role ID is required.']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT permission_id FROM role_permissions WHERE role_id = ?");
        $stmt->execute([$role_id]);
        $perms = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo json_encode(['success' => true, 'permissions' => $perms]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} 
elseif ($action === 'save_role') {
    $role_id = $_POST['role_id'] ?? null;
    $role_name = $_POST['role_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $permissions = $_POST['permissions'] ?? []; // Array of permission IDs

    if (empty($role_name)) {
        echo json_encode(['success' => false, 'message' => 'Role name is required.']);
        exit();
    }

    try {
        $pdo->beginTransaction();

        if ($role_id) {
            // Update existing role
            $stmt = $pdo->prepare("UPDATE roles_config SET role_name = ?, description = ? WHERE roleId = ?");
            $stmt->execute([$role_name, $description, $role_id]);
        } else {
            // Check if role name exists
            $stmt = $pdo->prepare("SELECT roleId FROM roles_config WHERE role_name = ?");
            $stmt->execute([$role_name]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Role name already exists.']);
                $pdo->rollBack();
                exit();
            }

            // Insert new role
            $stmt = $pdo->prepare("INSERT INTO roles_config (role_name, description) VALUES (?, ?)");
            $stmt->execute([$role_name, $description]);
            $role_id = $pdo->lastInsertId();
        }

        // Update permissions: Delete old and insert new
        $stmt = $pdo->prepare("DELETE FROM role_permissions WHERE role_id = ?");
        $stmt->execute([$role_id]);

        if (!empty($permissions)) {
            $stmt = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
            foreach ($permissions as $perm_id) {
                $stmt->execute([$role_id, $perm_id]);
            }
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Role and permissions saved successfully!']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
elseif ($action === 'delete_role') {
    $role_id = $_POST['role_id'] ?? null;
    if (!$role_id) {
        echo json_encode(['success' => false, 'message' => 'Role ID is required.']);
        exit();
    }

    try {
        $pdo->beginTransaction();
        
        // Delete permissions first
        $stmt = $pdo->prepare("DELETE FROM role_permissions WHERE role_id = ?");
        $stmt->execute([$role_id]);

        // Delete role
        $stmt = $pdo->prepare("DELETE FROM roles_config WHERE roleId = ?");
        $stmt->execute([$role_id]);

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Role deleted successfully.']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
else {
    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}
?>
