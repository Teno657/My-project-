<?php
session_start();
require_once 'db_connect.php';
require_once 'auth_function.php';

checkAdminOrUserLogin();

// Récupération de l'ID de la tâche
$task_id = $_GET['id'] ?? '';

if ($task_id) {
    // Stocker temporairement la tâche à supprimer (avec horodatage)
    $_SESSION['delete_task_pending'] = [
        'task_id' => $task_id,
        'start_time' => time()
    ];
}

// Rediriger vers la page des tâches avec l'overlay
header('Location: task.php');
exit;
