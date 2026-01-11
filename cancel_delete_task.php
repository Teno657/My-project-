<?php
session_start();
require_once 'auth_function.php';

checkAdminOrUserLogin();

// Si une suppression était en cours, on l'annule
if (isset($_SESSION['delete_task_pending'])) {
    unset($_SESSION['delete_task_pending']);
}

// Retour à la liste des tâches
header('Location: task.php');
exit;
