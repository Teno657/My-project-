<?php

require_once 'db_connect.php';
require_once 'auth_function.php';

checkAdminLogin();

$department_id = $_GET['id'] ?? '';
$department_name = '';
$department_status = 'Enable';
$message = '';
$message_type = 'danger'; // pour le type d'alerte bootstrap

// Récupération des données du département
if (!empty($department_id)) {
    $stmt = $pdo->prepare("SELECT * FROM task_department WHERE department_id = :department_id");
    $stmt->execute(['department_id' => $department_id]);
    $department = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($department) {
        $department_name = $department['department_name'];
        $department_status = $department['department_status'];
    } else {
        $message = 'Département introuvable.';
    }
}

// Soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $department_name = trim($_POST['department_name']);
    $department_status = trim($_POST['department_status']);
    $department_id = $_POST['department_id'];

    if (empty($department_name)) {
        $message = 'Le nom du département est requis.';
    } elseif (!in_array($department_status, ['Enable', 'Disable'])) {
        $message = 'Statut du département invalide.';
    } else {
        // Vérifier si le nom du département existe déjà (autre que celui modifié)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM task_department WHERE department_name = :department_name AND department_id != :department_id");
        $stmt->execute([
            'department_name' => $department_name,
            'department_id' => $department_id
        ]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $message = 'Un département avec ce nom existe déjà.';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE task_department SET department_name = :department_name, department_status = :department_status, department_updated_on = NOW() WHERE department_id = :department_id");
                $stmt->execute([
                    'department_name' => $department_name,
                    'department_status' => $department_status,
                    'department_id' => $department_id
                ]);
                header('Location: department.php');
                exit;
            } catch (PDOException $e) {
                $message = 'Erreur base de données : ' . $e->getMessage();
            }
        }
    }
}

include('header.php');
?>

<h1 class="mt-4 mb-3">Modifier un département</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="department.php">Gestion des départements</a></li>
    <li class="breadcrumb-item active">Modifier un département</li>
</ol>

<div class="row justify-content-center">
    <div class="col-md-6">
        <?php if($message): ?>
            <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-primary">
            <div class="card-header bg-primary text-white fw-bold">Modifier un département</div>
            <div class="card-body">
                <form method="post" action="edit_department.php?id=<?= htmlspecialchars($department_id) ?>" novalidate>
                    <div class="mb-3">
                        <label for="department_name" class="form-label">Nom du département <span class="text-danger">*</span></label>
                        <input type="text" id="department_name" name="department_name" class="form-control" required
                               value="<?= htmlspecialchars($department_name) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="department_status" class="form-label">Statut du département</label>
                        <select id="department_status" name="department_status" class="form-select" required>
                            <option value="Enable" <?= $department_status === 'Enable' ? 'selected' : '' ?>>Activer</option>
                            <option value="Disable" <?= $department_status === 'Disable' ? 'selected' : '' ?>>Désactiver</option>
                        </select>
                    </div>
                    <input type="hidden" name="department_id" value="<?= htmlspecialchars($department_id) ?>">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold">Enregistrer les modifications</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Animate.css pour animations -->
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
/>

<?php
include('footer.php');
?>
