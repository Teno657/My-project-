<?php
session_start();
require_once 'db_connect.php';
require_once 'auth_function.php';
checkAdminLogin();

header('Content-Type: application/json');

$user_id = $_GET['id'] ?? '';
if (empty($user_id)) {
    echo json_encode(['success' => false, 'message' => 'ID manquant']);
    exit;
}

try {
    // Vérifier s'il a des tâches
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM task_manage WHERE task_user_to = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $task_count = $stmt->fetchColumn();

    // Supprimer les tâches si elles existent
    if ($task_count > 0) {
        $stmt = $pdo->prepare("DELETE FROM task_manage WHERE task_user_to = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $message = "Suppression du stagiaire et de ses tâches effectuée avec succès.";
    } else {
        $message = "Suppression du stagiaire effectuée avec succès.";
    }

    // Supprimer le stagiaire
    $stmt = $pdo->prepare("DELETE FROM task_user WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);

    $_SESSION['success_message'] = $message;
    $_SESSION['message_origin'] = 'user';

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    file_put_contents('error_delete_log.txt', date('Y-m-d H:i:s') . " - " . $e->getMessage() . PHP_EOL, FILE_APPEND);

    $_SESSION['error_message'] = "Erreur lors de la suppression : " . $e->getMessage();
    $_SESSION['message_origin'] = 'user';

    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit;
