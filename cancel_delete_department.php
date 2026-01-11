<?php
session_start();
if (isset($_SESSION['pending_delete_department'])) {
    unset($_SESSION['pending_delete_department']);
}
echo json_encode(['status' => 'cancelled']);
exit;
