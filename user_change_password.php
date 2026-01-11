<?php
require_once 'db_connect.php';
require_once 'auth_function.php';

checkAdminOrUserLogin();

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id           = $_SESSION['user_id'] ?? null;
    $current_password  = trim($_POST['current_password'] ?? '');
    $new_password      = trim($_POST['new_password'] ?? '');
    $confirm_password  = trim($_POST['confirm_password'] ?? '');

    // Validation
    if (!$current_password)   $errors[] = 'Le mot de passe actuel est requis.';
    if (!$new_password)       $errors[] = 'Le nouveau mot de passe est requis.';
    if (!$confirm_password)   $errors[] = 'La confirmation du mot de passe est requise.';
    if ($new_password && $confirm_password && $new_password !== $confirm_password) {
        $errors[] = 'Les mots de passe ne correspondent pas.';
    }

    // Si pas d’erreur, vérifier mot de passe actuel
    if (!$errors && $user_id) {
        $stmt = $pdo->prepare("SELECT user_email_password FROM task_user WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($current_password, $user['user_email_password'])) {
            // Hasher et mettre à jour
            $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE task_user SET user_email_password = ? WHERE user_id = ?");
            $stmt->execute([$new_password_hashed, $user_id]);
            $success = true;
        } else {
            $errors[] = 'Le mot de passe actuel est incorrect.';
        }
    }
}

include('header.php');
?>

<h1 class="mt-4 text-primary fw-bold">Changer le mot de passe</h1>
<ol class="breadcrumb mb-4 bg-light rounded p-2">
    <li class="breadcrumb-item"><a href="task.php" class="text-decoration-none text-primary">Tâches</a></li>
    <li class="breadcrumb-item active">Changer le mot de passe</li>
</ol>

<div class="row">
    <div class="col-md-5">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif ($success): ?>
            <div class="alert alert-success">Mot de passe mis à jour avec succès.</div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">Modifier le mot de passe</div>
            <div class="card-body">
                <form method="POST" action="user_change_password.php">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mot de passe actuel</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmer le nouveau mot de passe</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary px-4">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
