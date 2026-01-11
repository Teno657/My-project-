<?php
session_start();
require_once 'db_connect.php';
require_once 'auth_function.php';
checkAdminLogin();

$user_id = $_GET['id'] ?? '';

if (!empty($user_id)) {
    $_SESSION['pending_delete_user'] = ['user_id' => $user_id];
}

header('Location: user.php');
exit;
