<?php
session_start();
require_once 'db_connect.php';
require_once 'auth_function.php';
checkAdminLogin();

header('Content-Type: application/json');

$department_id = $_GET['id'] ?? '';
if (empty($department_id)) {
    echo json_encode(['success' => false, 'message' => 'ID manquant']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM task_department WHERE department_id = :id");
    $stmt->execute(['id' => $department_id]);

    $_SESSION['success_message'] = "Département supprimé avec succès.";
    $_SESSION['message_origin'] = 'department';

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $_SESSION['error_message'] = "Erreur lors de la suppression du département.";
    $_SESSION['message_origin'] = 'department';

    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit;
