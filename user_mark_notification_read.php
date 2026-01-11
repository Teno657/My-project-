<?php
require_once 'db_connect.php';
require_once 'auth_function.php';

checkUserLogin();

$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    $stmt = $pdo->prepare("UPDATE user_notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->execute([$user_id]);
}

// Tu peux rediriger vers la page des notifications ou renvoyer un JSON
header("Location: user_notifications.php");
exit;
?>
