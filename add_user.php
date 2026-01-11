<?php 

require_once 'db_connect.php';
require_once 'auth_function.php';

checkAdminLogin();

$message = '';

// Fetch departments for the dropdown
$departments = [];
$stmt = $pdo->query("SELECT department_id, department_name FROM task_department WHERE department_status = 'Enable'");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $departments[] = $row;
}

// Initialize variables to avoid undefined notices
$user_first_name = $user_last_name = $department_id = $user_email_address = '';
$user_email_password = $user_contact_no = $user_date_of_birth = $user_gender = '';
$user_address = $user_status = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération et nettoyage des données
    $user_first_name = trim($_POST['user_first_name']);
    $user_last_name = trim($_POST['user_last_name']);
    $department_id = trim($_POST['department_id']);
    $user_email_address = trim($_POST['user_email_address']);
    $user_email_password = trim($_POST['user_email_password']);
    $user_contact_no = trim($_POST['user_contact_no']);
    $user_date_of_birth = trim($_POST['user_date_of_birth']);
    $user_gender = trim($_POST['user_gender']);
    $user_address = trim($_POST['user_address']);
    $user_status = trim($_POST['user_status']);
    $user_image = $_FILES['user_image'];

    // Validation des champs obligatoires
    if (
        empty($user_first_name) || empty($user_last_name) || empty($department_id) || empty($user_email_address) ||
        empty($user_email_password) || empty($user_contact_no) || empty($user_date_of_birth) || empty($user_gender) ||
        empty($user_address) || empty($user_status)
    ) {
        $message = 'Tous les champs sont obligatoires.';
    } elseif (!filter_var($user_email_address, FILTER_VALIDATE_EMAIL)) {
        $message = 'Format d\'email invalide.';
    } elseif ($user_image['error'] !== UPLOAD_ERR_OK) {
        $message = 'Erreur lors du téléchargement de l\'image.';
    } else {
        // Vérifier que l'email n'existe pas déjà
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM task_user WHERE user_email_address = :user_email_address");
        $stmt->execute(['user_email_address' => $user_email_address]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $message = 'Cet email existe déjà.';
        } else {
            // Hash du mot de passe
            $hashed_password = password_hash($user_email_password, PASSWORD_DEFAULT);

            // Générer un nom unique pour l'image pour éviter écrasement
            $image_extension = pathinfo($user_image['name'], PATHINFO_EXTENSION);
            $unique_image_name = uniqid('user_', true) . '.' . $image_extension;
            $image_path = 'uploads/' . $unique_image_name;

            if (move_uploaded_file($user_image['tmp_name'], $image_path)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO task_user (user_first_name, user_last_name, department_id, user_email_address, user_email_password, user_contact_no, user_date_of_birth, user_gender, user_address, user_status, user_image, user_added_on, user_updated_on) VALUES (:user_first_name, :user_last_name, :department_id, :user_email_address, :user_email_password, :user_contact_no, :user_date_of_birth, :user_gender, :user_address, :user_status, :user_image, NOW(), NOW())");
                    $stmt->execute([
                        'user_first_name'       => $user_first_name,
                        'user_last_name'        => $user_last_name,
                        'department_id'         => $department_id,
                        'user_email_address'    => $user_email_address,
                        'user_email_password'   => $hashed_password,
                        'user_contact_no'       => $user_contact_no,
                        'user_date_of_birth'    => $user_date_of_birth,
                        'user_gender'           => $user_gender,
                        'user_address'          => $user_address,
                        'user_status'           => $user_status,
                        'user_image'            => $image_path
                    ]);
                    session_start(); // Ajoute cette ligne ici pour démarrer la session (au cas où)
$_SESSION['success_message'] = "Le stagiaire a été ajouté avec succès.";
$_SESSION['message_origin'] = "user";
                    header('Location: user.php');
                    exit;
                } catch (PDOException $e) {
                    $message = 'Erreur base de données : ' . $e->getMessage();
                    // Supprimer le fichier uploadé en cas d'erreur DB pour éviter fichiers orphanes
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
            } else {
                $message = 'Impossible de déplacer le fichier image uploadé.';
            }
        }
    }
}

include('header.php');
?>

<h1 class="mt-4">Ajouter un stagiaire</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="user.php">Gestion des stagiaires</a></li>
    <li class="breadcrumb-item active">Ajouter un stagiaire</li>
</ol>

<?php if ($message !== ''): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Ajouter un stagiaire</div>
    <div class="card-body">
        <form method="post" action="add_user.php" enctype="multipart/form-data">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="department_id">Département :</label>
                    <select id="department_id" name="department_id" class="form-select" required>
                        <option value="">Sélectionner un département</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= htmlspecialchars($dept['department_id']) ?>" <?= ($dept['department_id'] == $department_id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dept['department_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="user_first_name">Nom :</label>
                    <input type="text" id="user_first_name" name="user_first_name" class="form-control" value="<?= htmlspecialchars($user_first_name) ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="user_last_name">Prénom :</label>
                    <input type="text" id="user_last_name" name="user_last_name" class="form-control" value="<?= htmlspecialchars($user_last_name) ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="user_email_address">Email :</label>
                    <input type="email" id="user_email_address" name="user_email_address" class="form-control" value="<?= htmlspecialchars($user_email_address) ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="user_email_password">Mot de passe :</label>
                    <input type="password" id="user_email_password" name="user_email_password" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label for="user_contact_no">Contact :</label>
                    <input type="text" id="user_contact_no" name="user_contact_no" class="form-control" value="<?= htmlspecialchars($user_contact_no) ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="user_date_of_birth">Date de naissance :</label>
                    <input type="date" id="user_date_of_birth" name="user_date_of_birth" class="form-control" value="<?= htmlspecialchars($user_date_of_birth) ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="user_gender">Genre :</label>
                    <select id="user_gender" name="user_gender" class="form-select" required>
                        <option value="">Sélectionner un genre</option>
                        <option value="Male" <?= ($user_gender == 'Male') ? 'selected' : '' ?>>Masculin</option>
                        <option value="Female" <?= ($user_gender == 'Female') ? 'selected' : '' ?>>Féminin</option>
                        <option value="Other" <?= ($user_gender == 'Other') ? 'selected' : '' ?>>Autre</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="user_address">Adresse :</label>
                    <input type="text" id="user_address" name="user_address" class="form-control" value="<?= htmlspecialchars($user_address) ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="user_image">Photo du stagiaire :</label>
                    <input type="file" id="user_image" name="user_image" accept="image/*" required>
                </div>
                <div class="col-md-4">
                    <label for="user_status">Statut :</label>
                    <select id="user_status" name="user_status" class="form-select" required>
                        <option value="Enable" <?= ($user_status == 'Enable') ? 'selected' : '' ?>>Activer</option>
                        <option value="Disable" <?= ($user_status == 'Disable') ? 'selected' : '' ?>>Désactiver</option>
                    </select>
                </div>
            </div>

            <div class="mt-2 text-center">
                <input type="submit" value="Ajouter un stagiaire" class="btn btn-primary" />
            </div>
        </form>
    </div>
</div>

<?php
include('footer.php');
?>
