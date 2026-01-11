<?php
session_start();
require_once 'db_connect.php';
require_once 'auth_function.php';

checkAdminLogin();

$task_id = $_POST['task_id'] ?? '';

if (!empty($task_id)) {
    $stmt = $pdo->prepare("DELETE FROM task_manage WHERE task_id = :task_id");
    $stmt->execute(['task_id' => $task_id]);

    unset($_SESSION['delete_task_pending']); // Nettoyer la session

    $_SESSION['success_message'] = "Tâche supprimée avec succès.";
    $_SESSION['message_origin'] = 'task';

    header('Location: task.php');
    exit;
} else {
    $_SESSION['error_message'] = "ID de tâche manquant.";
    $_SESSION['message_origin'] = 'task';

    header('Location: task.php');
    exit;
}
