<?php
session_start();
require_once 'db_connect.php';

// Vérifier que l'admin est connecté
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php'); // ou une autre page de connexion
    exit;
}

// Vérifier que l'ID de notification est reçu en POST
if (isset($_POST['notification_id']) && is_numeric($_POST['notification_id'])) {
    $notification_id = (int) $_POST['notification_id'];

    // Préparer et exécuter la suppression
    $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = :id");
    $stmt->execute(['id' => $notification_id]);

    // Redirection vers la page précédente ou une page souhaitée
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'dashboard.php'));
    exit;
} else {
    // Mauvaise requête
    header('HTTP/1.1 400 Bad Request');
    echo "Requête invalide.";
    exit;
}
