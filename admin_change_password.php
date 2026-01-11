<?php 

require_once 'db_connect.php';
require_once 'auth_function.php';

checkAdminLogin();
$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $admin_id = $_SESSION['admin_id'];
    $current_password = trim($_POST['current_password'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validation
    if (empty($current_password)) {
        $errors[] = 'Le mot de passe actuel est requis.';
    }
    if (empty($new_password)) {
        $errors[] = 'Le nouveau mot de passe est requis.';
    }
    if (empty($confirm_password)) {
        $errors[] = 'La confirmation du nouveau mot de passe est requise.';
    }
    if ($new_password !== $confirm_password) {
        $errors[] = 'Le nouveau mot de passe et la confirmation ne correspondent pas.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT admin_password FROM task_admin WHERE admin_id = ?");
        $stmt->execute([$admin_id]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($current_password, $admin['admin_password'])) {
            $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE task_admin SET admin_password = ? WHERE admin_id = ?");
            if ($stmt->execute([$new_password_hashed, $admin_id])) {
                $success = true;
            } else {
                $errors[] = "Erreur lors de la mise à jour du mot de passe. Veuillez réessayer.";
            }
        } else {
            $errors[] = 'Le mot de passe actuel est incorrect.';
        }
    }
}

include('header.php');
?>

<div class="container my-5" style="max-width: 450px;">
    <h1 class="mb-4 text-center text-primary fw-bold">Changer de mot de passe</h1>
    <ol class="breadcrumb mb-4 bg-light p-3 rounded shadow-sm">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Changer de mot de passe</li>
    </ol>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger shadow-sm" role="alert">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success shadow-sm" role="alert">
            Le mot de passe a été changé avec succès.
        </div>
    <?php endif; ?>

    <div class="card shadow border-0 rounded">
        <div class="card-body p-4">
            <form id="changePasswordForm" method="POST" novalidate>
                <div class="mb-3">
                    <label for="current_password" class="form-label fw-semibold">Mot de passe actuel <span class="text-danger">*</span></label>
                    <input type="password" name="current_password" id="current_password" class="form-control" required placeholder="Entrez votre mot de passe actuel" autocomplete="current-password" >
                    <div class="invalid-feedback">Veuillez entrer votre mot de passe actuel.</div>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label fw-semibold">Nouveau mot de passe <span class="text-danger">*</span></label>
                    <input type="password" name="new_password" id="new_password" class="form-control" required placeholder="Nouveau mot de passe" autocomplete="new-password">
                    <div class="invalid-feedback">Veuillez entrer un nouveau mot de passe.</div>
                </div>
                <div class="mb-4">
                    <label for="confirm_password" class="form-label fw-semibold">Confirmer le nouveau mot de passe <span class="text-danger">*</span></label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required placeholder="Confirmez le nouveau mot de passe" autocomplete="new-password">
                    <div class="invalid-feedback">Veuillez confirmer votre nouveau mot de passe.</div>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm" style="font-size:1.1rem; transition: background-color 0.3s ease;">
                    <i class="fas fa-key me-2"></i>Changer le mot de passe
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// Bootstrap 5 validation
(() => {
    'use strict';
    const form = document.getElementById('changePasswordForm');
    form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
})();
</script>

<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        font-family: 'Inter', sans-serif;
        min-height: 100vh;
    }
    h1 {
        text-shadow: 0 0 8px rgba(255, 255, 255, 0.7);
    }
    .card {
        border-radius: 15px;
        transition: box-shadow 0.3s ease;
        background: #fff;
    }
    .card:hover {
        box-shadow: 0 10px 30px rgba(118, 75, 162, 0.3);
    }
    label {
        color: #333;
    }
    .form-control:focus {
        border-color: #764ba2;
        box-shadow: 0 0 8px rgba(118, 75, 162, 0.6);
        outline: none;
    }
    .btn-primary:hover {
        background-color: #5a3399 !important;
    }
    .breadcrumb {
        font-weight: 600;
        color: #5a3399;
    }
    .breadcrumb a {
        color: #764ba2;
        text-decoration: none;
    }
    .breadcrumb a:hover {
        text-decoration: underline;
    }
</style>

<?php
include('footer.php');
?>
