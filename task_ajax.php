<?php
session_start();
require_once 'db_connect.php';
require_once 'auth_function.php';

$isAdmin = !empty($_SESSION['admin_logged_in']);
$user_id = $_SESSION['user_id'] ?? null;

$columns = [
    0 => 'task_manage.task_id',
    1 => 'task_department.department_name',
    2 => 'task_user.user_first_name',
    3 => 'task_manage.task_title',
    4 => 'task_manage.task_assign_date',
    5 => 'task_manage.task_end_date',
    6 => 'task_manage.task_status',
];

$searchValue = $_GET['search']['value'] ?? '';
$limit = (int) ($_GET['length'] ?? 10);
$offset = (int) ($_GET['start'] ?? 0);
$orderColumnIndex = (int) ($_GET['order'][0]['column'] ?? 0);
$orderDir = strtoupper($_GET['order'][0]['dir'] ?? 'ASC');
$orderDir = in_array($orderDir, ['ASC', 'DESC']) ? $orderDir : 'ASC';
$orderColumn = $columns[$orderColumnIndex] ?? 'task_manage.task_id';

// Construction WHERE
$where = "";
$params = [];

if (!$isAdmin) {
    $where = " WHERE task_manage.task_user_to = :user_id ";
    $params[':user_id'] = $user_id;
}

if ($searchValue !== '') {
    $searchSql = " (task_department.department_name LIKE :search OR task_manage.task_title LIKE :search OR task_manage.task_status LIKE :search) ";
    if ($where === "") {
        $where = " WHERE $searchSql ";
    } else {
        $where .= " AND $searchSql ";
    }
    $params[':search'] = "%$searchValue%";
}

// Comptage total (sans filtre)
if ($isAdmin) {
    $stmtTotal = $pdo->query("SELECT COUNT(*) FROM task_manage");
    $totalRecords = $stmtTotal->fetchColumn();
} else {
    $stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM task_manage WHERE task_user_to = :user_id");
    $stmtTotal->execute([':user_id' => $user_id]);
    $totalRecords = $stmtTotal->fetchColumn();
}

// Comptage filtré
$countFilteredSql = "SELECT COUNT(*) FROM task_manage 
    JOIN task_department ON task_manage.task_department_id = task_department.department_id
    JOIN task_user ON task_manage.task_user_to = task_user.user_id
    $where";
$stmtFiltered = $pdo->prepare($countFilteredSql);
foreach ($params as $k => $v) {
    if ($k === ':user_id') {
        $stmtFiltered->bindValue($k, $v, PDO::PARAM_INT);
    } else {
        $stmtFiltered->bindValue($k, $v, PDO::PARAM_STR);
    }
}
$stmtFiltered->execute();
$filteredRecords = $stmtFiltered->fetchColumn();

// Requête principale
$query = "SELECT task_manage.*, task_department.department_name, task_user.user_first_name, task_user.user_last_name, task_user.user_image
    FROM task_manage
    JOIN task_department ON task_manage.task_department_id = task_department.department_id
    JOIN task_user ON task_manage.task_user_to = task_user.user_id
    $where
    ORDER BY $orderColumn $orderDir
    LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);
foreach ($params as $k => $v) {
    if ($k === ':user_id') {
        $stmt->bindValue($k, $v, PDO::PARAM_INT);
    } else {
        $stmt->bindValue($k, $v, PDO::PARAM_STR);
    }
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    "draw" => intval($_GET['draw'] ?? 0),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($filteredRecords),
    "data" => $data
], JSON_UNESCAPED_UNICODE);
exit;
