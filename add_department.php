<?php

require_once 'db_connect.php';
require_once 'auth_function.php';

checkAdminLogin();

$message = '';
$message_type = ''; // 'danger' ou 'success'

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $department_name = trim($_POST['department_name']);
    $department_status = trim($_POST['department_status']);

    // Validation
    if (empty($department_name)) {
        $message = 'Le nom du département est requis.';
        $message_type = 'danger';
    } elseif (!in_array($department_status, ['Enable', 'Disable'])) {
        $message = 'Statut du département invalide.';
        $message_type = 'danger';
    } else {
        // Vérifier si le département existe déjà
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM task_department WHERE department_name = :department_name");
        $stmt->execute(['department_name' => $department_name]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $message = 'Un département avec ce nom existe déjà.';
            $message_type = 'danger';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO task_department (department_name, department_status, department_added_on, department_updated_on) VALUES (:department_name, :department_status, NOW(), NOW())");
                $stmt->execute([
                    'department_name' => $department_name,
                    'department_status' => $department_status
                ]);
                $message = 'Département ajouté avec succès !';
                $message_type = 'success';
            } catch (PDOException $e) {
                $message = 'Erreur base de données : ' . $e->getMessage();
                $message_type = 'danger';
            }
        }
    }
}

include('header.php');
?>

<h1 class="mt-4 mb-3">Ajouter un département</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="department.php">Gestion des départements</a></li>
    <li class="breadcrumb-item active">Ajouter un département</li>
</ol>

<div class="row justify-content-center">
    <div class="col-md-6">
        <?php if($message): ?>
            <div class="alert alert-<?= htmlspecialchars($message_type) ?> alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-primary">
            <div class="card-header bg-primary text-white fw-bold">Ajouter un département</div>
            <div class="card-body">
                <form method="post" action="add_department.php" novalidate>
                    <div class="mb-3">
                        <label for="department_name" class="form-label">Nom du département <span class="text-danger">*</span></label>
                        <input type="text" id="department_name" name="department_name" class="form-control" required
                               value="<?= isset($_POST['department_name']) ? htmlspecialchars($_POST['department_name']) : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label for="department_status" class="form-label">Statut du département</label>
                        <select id="department_status" name="department_status" class="form-select" required>
                            <option value="Enable" <?= (isset($_POST['department_status']) && $_POST['department_status'] === 'Enable') ? 'selected' : '' ?>>Activer</option>
                            <option value="Disable" <?= (isset($_POST['department_status']) && $_POST['department_status'] === 'Disable') ? 'selected' : '' ?>>Désactiver</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-semibold">Ajouter le département</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Ajouter Animate.css pour l'animation -->
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
/>

<?php
include('footer.php');
?>
