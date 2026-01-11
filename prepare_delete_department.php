<?php
session_start();
require_once 'db_connect.php';
require_once 'auth_function.php';
checkAdminLogin();

$department_id = $_GET['id'] ?? '';

if (!empty($department_id)) {
    $_SESSION['pending_delete_department'] = ['department_id' => $department_id];
}

header('Location: department.php');
exit;
