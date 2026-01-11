<?php
session_start();
if (isset($_SESSION['pending_delete_user'])) {
    unset($_SESSION['pending_delete_user']);
}
echo json_encode(['status' => 'cancelled']);
exit;
