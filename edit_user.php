<?php 

require_once 'db_connect.php';
require_once 'auth_function.php';

checkAdminLogin();

$message = '';
$user_id = $_GET['id'] ?? '';
$user_first_name = '';
$user_last_name = '';
$department_id = '';
$user_email_address = '';
$user_contact_no = '';
$user_date_of_birth = '';
$user_gender = 'Male';
$user_address = '';
$user_status = 'Enable';
$user_image = '';

// Récupération des départements pour le select
$departments = [];
$stmt = $pdo->query("SELECT department_id, department_name FROM task_department WHERE department_status = 'Enable'");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $departments[] = $row;
}

// Récupérer les données du stagiaire
if (!empty($user_id)) {
    $stmt = $pdo->prepare("SELECT * FROM task_user WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $user_first_name = $user['user_first_name'];
        $user_last_name = $user['user_last_name'];
        $department_id = $user['department_id'];
        $user_email_address = $user['user_email_address'];
        $user_contact_no = $user['user_contact_no'];
        $user_date_of_birth = $user['user_date_of_birth'];
        $user_gender = $user['user_gender'];
        $user_address = $user['user_address'];
        $user_status = $user['user_status'];
        $user_image = $user['user_image'];
    } else {
        $message = 'Stagiaire non trouvé.';
    }
}

// Gestion de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_first_name = trim($_POST['user_first_name']);
    $user_last_name = trim($_POST['user_last_name']);
    $department_id = trim($_POST['department_id']);
    $user_email_address = trim($_POST['user_email_address']);
    $user_contact_no = trim($_POST['user_contact_no']);
    $user_date_of_birth = trim($_POST['user_date_of_birth']);
    $user_gender = trim($_POST['user_gender']);
    $user_address = trim($_POST['user_address']);
    $user_status = trim($_POST['user_status']);
    $new_image = $_FILES['user_image'];

    // Validation des champs obligatoires
    if (
        empty($user_first_name) || empty($user_last_name) || empty($department_id) ||
        empty($user_email_address) || empty($user_contact_no) || empty($user_date_of_birth) ||
        empty($user_gender) || empty($user_address) || empty($user_status)
    ) {
        $message = 'Tous les champs sont obligatoires.';
    } elseif (!filter_var($user_email_address, FILTER_VALIDATE_EMAIL)) {
        $message = 'Format d\'email invalide.';
    } elseif ($new_image['error'] !== UPLOAD_ERR_NO_FILE && $new_image['error'] !== UPLOAD_ERR_OK) {
        $message = 'Erreur lors de l\'upload de l\'image.';
    } else {
        // Vérifier si l'email existe déjà pour un autre utilisateur
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM task_user WHERE user_email_address = :email AND user_id != :id");
        $stmt->execute(['email' => $user_email_address, 'id' => $user_id]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $message = 'Cet email est déjà utilisé.';
        } else {
            // Gérer l'upload de la nouvelle image si présente
            if ($new_image['error'] === UPLOAD_ERR_OK) {
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                $file_ext = strtolower(pathinfo($new_image['name'], PATHINFO_EXTENSION));

                if (!in_array($file_ext, $allowed_extensions)) {
                    $message = 'Format d\'image non autorisé. Utilisez jpg, jpeg, png ou gif.';
                } else {
                    $upload_dir = 'uploads/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $new_filename = uniqid() . '.' . $file_ext;
                    $image_path = $upload_dir . $new_filename;

                    if (move_uploaded_file($new_image['tmp_name'], $image_path)) {
                        $user_image = $image_path;
                    } else {
                        $message = 'Impossible de téléverser l\'image.';
                    }
                }
            }

            // Si pas d'erreur, mettre à jour la base
            if (empty($message)) {
                try {
                    $stmt = $pdo->prepare("UPDATE task_user SET user_first_name = :first_name, user_last_name = :last_name, department_id = :department_id, user_email_address = :email, user_contact_no = :contact, user_date_of_birth = :dob, user_gender = :gender, user_address = :address, user_status = :status, user_image = :image, user_updated_on = NOW() WHERE user_id = :id");
                    $stmt->execute([
                        'first_name'    => $user_first_name,
                        'last_name'     => $user_last_name,
                        'department_id' => $department_id,
                        'email'         => $user_email_address,
                        'contact'       => $user_contact_no,
                        'dob'           => $user_date_of_birth,
                        'gender'        => $user_gender,
                        'address'       => $user_address,
                        'status'        => $user_status,
                        'image'         => $user_image,
                        'id'            => $user_id
                    ]);
                   // Mettre le message flash dans la session
$_SESSION['success_message'] = "Le stagiaire a été modifié avec succès.";
$_SESSION['message_origin'] = 'user';

// Redirection vers la page user.php pour afficher la liste
header('Location: user.php');
exit;

                } catch (PDOException $e) {
                    $message = 'Erreur base de données : ' . $e->getMessage();
                }
            }
        }
    }
}

include('header.php');
?>

<h1 class="mt-4">Modifier un stagiaire</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="user.php">Gestion des stagiaires</a></li>
    <li class="breadcrumb-item active">Modifier un stagiaire</li>
</ol>

<?php if ($message !== ''): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Modifier un stagiaire</div>
    <div class="card-body">
        <form method="post" action="edit_user.php?id=<?php echo htmlspecialchars($user_id); ?>" enctype="multipart/form-data">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="department_id">Département:</label>
                    <select id="department_id" name="department_id" class="form-select" required>
                        <option value="">Sélectionner un département</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['department_id']; ?>" <?php if ($department_id == $dept['department_id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($dept['department_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="user_first_name">Nom:</label>
                    <input type="text" id="user_first_name" name="user_first_name" class="form-control" value="<?php echo htmlspecialchars($user_first_name); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="user_last_name">Prénom:</label>
                    <input type="text" id="user_last_name" name="user_last_name" class="form-control" value="<?php echo htmlspecialchars($user_last_name); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="user_email_address">Email:</label>
                    <input type="email" id="user_email_address" name="user_email_address" class="form-control" value="<?php echo htmlspecialchars($user_email_address); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="user_contact_no">Contact:</label>
                    <input type="text" id="user_contact_no" name="user_contact_no" class="form-control" value="<?php echo htmlspecialchars($user_contact_no); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="user_gender">Genre:</label>
                    <select id="user_gender" name="user_gender" class="form-select" required>
                        <option value="Male" <?php if ($user_gender == 'Male') echo 'selected'; ?>>Masculin</option>
                        <option value="Female" <?php if ($user_gender == 'Female') echo 'selected'; ?>>Féminin</option>
                        <option value="Other" <?php if ($user_gender == 'Other') echo 'selected'; ?>>Autre</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="user_date_of_birth">Date de naissance:</label>
                    <input type="date" id="user_date_of_birth" name="user_date_of_birth" class="form-control" value="<?php echo htmlspecialchars($user_date_of_birth); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="user_address">Adresse:</label>
                    <input type="text" id="user_address" name="user_address" class="form-control" value="<?php echo htmlspecialchars($user_address); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="user_image">Photo du stagiaire:</label><br />
                    <input type="file" id="user_image" name="user_image" accept="image/*">
                    <?php if ($user_image): ?>
                        <div class="mt-2">
                            <img src="<?php echo htmlspecialchars($user_image); ?>" class="img-thumbnail" alt="User Image" width="100">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label for="user_status">Statut:</label>
                    <select id="user_status" name="user_status" class="form-select" required>
                        <option value="Enable" <?php if ($user_status == 'Enable') echo 'selected'; ?>>Activé</option>
                        <option value="Disable" <?php if ($user_status == 'Disable') echo 'selected'; ?>>Désactivé</option>
                    </select>
                </div>
            </div>
            <div class="mt-2 text-center">
                <input type="submit" value="Modifier" class="btn btn-primary" />
            </div>
        </form>
    </div>
</div>

<?php
include('footer.php');
?>
