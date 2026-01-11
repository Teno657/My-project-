<?php

require_once 'db_connect.php';

$columns = [
    1 => 'user_id',
    2 => 'department_name',
    3 => 'user_first_name',
    4 => 'user_last_name',
    5 => 'user_email_address',
    6 => 'user_contact_no',
    7 => 'user_status'
];

// Lecture des paramètres DataTables
$limit = isset($_GET['length']) ? (int)$_GET['length'] : 10;
$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
$orderColumnIndex = isset($_GET['order'][0]['column']) ? (int)$_GET['order'][0]['column'] : 1;
$orderDir = isset($_GET['order'][0]['dir']) && in_array(strtoupper($_GET['order'][0]['dir']), ['ASC','DESC']) ? $_GET['order'][0]['dir'] : 'ASC';
$searchValue = $_GET['search']['value'] ?? '';

// Choix de la colonne à trier (valide uniquement si dans $columns)
$orderColumn = $columns[$orderColumnIndex] ?? 'user_id';

// Requête pour compter total
$totalRecordsStmt = $pdo->query("SELECT COUNT(*) FROM task_user");
$totalRecords = $totalRecordsStmt->fetchColumn();

// Construction de la clause WHERE avec filtre recherche
$where = " WHERE 1=1 ";
$params = [];

if (!empty($searchValue)) {
    $where .= " AND (
        task_department.department_name LIKE :search OR
        task_user.user_first_name LIKE :search OR
        task_user.user_last_name LIKE :search OR
        task_user.user_email_address LIKE :search OR
        task_user.user_contact_no LIKE :search OR
        task_user.user_status LIKE :search
    )";
    $params[':search'] = "%$searchValue%";
}

// Compter le total filtré
$sqlFilteredCount = "SELECT COUNT(*) FROM task_user LEFT JOIN task_department ON task_user.department_id = task_department.department_id $where";
$stmtFilteredCount = $pdo->prepare($sqlFilteredCount);
$stmtFilteredCount->execute($params);
$totalFilteredRecords = $stmtFilteredCount->fetchColumn();

// Requête principale avec limite, ordre et filtre
$sql = "SELECT task_user.*, task_department.department_name 
        FROM task_user 
        LEFT JOIN task_department ON task_user.department_id = task_department.department_id
        $where
        ORDER BY $orderColumn $orderDir
        LIMIT :start, :limit";

$stmt = $pdo->prepare($sql);

// Bind des paramètres
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val, PDO::PARAM_STR);
}
$stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
$stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Construction de la réponse JSON
$response = [
    "draw" => intval($_GET['draw'] ?? 0),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalFilteredRecords),
    "data" => $data
];

header('Content-Type: application/json');
echo json_encode($response);
