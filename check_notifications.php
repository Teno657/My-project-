<?php
session_start();
require_once 'db_connect.php';

$response = ['count' => 0];

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM notifications WHERE is_read = 0");
    $response['count'] = (int) $stmt->fetchColumn();
}

if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM task_manage WHERE task_user_to = ? AND task_status = 'Pending'");
    $stmt->execute([$user_id]);
    $response['count'] = (int) $stmt->fetchColumn();
}

header('Content-Type: application/json');
echo json_encode($response);
