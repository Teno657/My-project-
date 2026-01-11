<?php
require_once 'db_connect.php';
require_once 'auth_function.php';
session_start();
checkAdminLogin();

$user_id = $_GET['id'] ?? '';
$confirmed = $_GET['confirm'] ?? '';

if (!$confirmed && !empty($user_id)) {
    // Phase 1 : Affichage du bouton d’attente avec possibilité d’annuler
    $_SESSION['delete_pending'] = ['user_id' => $user_id];
    header('Location: user.php');
    exit;
}

if ($confirmed && !empty($user_id)) {
    // Phase 2 : Suppression réelle
    try {
        // Vérifier si le stagiaire a des tâches
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM task_manage WHERE task_user_to = ?");
        $stmt->execute([$user_id]);
        $taskCount = $stmt->fetchColumn();

        // Supprimer les tâches s’il y en a
        if ($taskCount > 0) {
            $pdo->prepare("DELETE FROM task_manage WHERE task_user_to = ?")->execute([$user_id]);
        }

        // Supprimer le stagiaire
        $pdo->prepare("DELETE FROM task_user WHERE user_id = ?")->execute([$user_id]);

        $_SESSION['success_message'] = $taskCount > 0 
            ? "Stagiaire et ses tâches supprimés avec succès." 
            : "Stagiaire supprimé avec succès.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Erreur lors de la suppression.";
    }

    header("Location: user.php");
    exit;
}
