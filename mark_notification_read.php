<?php
session_start();
require_once 'db_connect.php';

// Vérifier que l'admin est connecté
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['notification_id'])) {
    $notif_id = (int)$_POST['notification_id'];

    // Mettre à jour la notification pour marquer comme lue
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = :id");
    $stmt->execute(['id' => $notif_id]);
}

// Rediriger vers la page des notifications
header('Location: all_notifications.php');
exit;
