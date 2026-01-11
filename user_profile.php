<?php
/********************************************
 *  user_profile.php — version “pro” 2025  *
 ********************************************/
require_once 'db_connect.php';
require_once 'auth_function.php';

checkAdminOrUserLogin();

$user_id        = $_SESSION['user_id'] ?? null;
$message        = '';
$success        = false;
$user_first_name= $user_last_name = $user_email_address = $user_contact_no =
$user_date_of_birth = $user_gender = $user_address = $user_image = '';

/* ---------- 1. Récupération des infos courantes ---------- */
if ($user_id) {
    $stmt = $pdo->prepare("SELECT * FROM task_user WHERE user_id = :id");
    $stmt->execute(['id' => $user_id]);
    if ($u = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($u);   // crée les variables $user_first_name, etc.
    } else {
        $message = "Profil introuvable.";
    }
}

/* ---------- 2. Traitement du formulaire ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_first_name     = trim($_POST['user_first_name'] ?? '');
    $user_last_name      = trim($_POST['user_last_name'] ?? '');
    $user_email_address  = trim($_POST['user_email_address'] ?? '');
    $user_contact_no     = trim($_POST['user_contact_no'] ?? '');
    $user_date_of_birth  = trim($_POST['user_date_of_birth'] ?? '');
    $user_gender         = trim($_POST['user_gender'] ?? '');
    $user_address        = trim($_POST['user_address'] ?? '');
    $new_image           = $_FILES['user_image'] ?? null;

    /* — Validation — */
    $errors = [];
    foreach ([
        'Prénom' => $user_first_name,
        'Nom'    => $user_last_name,
        'Email'  => $user_email_address,
        'Contact'=> $user_contact_no,
        'Date'   => $user_date_of_birth,
        'Genre'  => $user_gender,
        'Adresse'=> $user_address
    ] as $label => $value) {
        if ($value === '') $errors[] = "$label requis.";
    }
    if ($user_email_address && !filter_var($user_email_address, FILTER_VALIDATE_EMAIL))
        $errors[] = "Format d’e‑mail invalide.";

    if ($new_image && $new_image['error'] === UPLOAD_ERR_OK) {
        $extOK = ['image/jpeg','image/png','image/webp'];
        if (!in_array($new_image['type'], $extOK))
            $errors[] = "Format d’image non supporté.";
    }

    /* — Unicité de l’e‑mail — */
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM task_user WHERE user_email_address = :mail AND user_id <> :id");
    $stmt->execute(['mail'=>$user_email_address, 'id'=>$user_id]);
    if ($stmt->fetchColumn()) $errors[] = "Cet e‑mail est déjà utilisé.";

    /* — Mise à jour — */
    if (!$errors) {
        if ($new_image && $new_image['error'] === UPLOAD_ERR_OK) {
            $folder = 'uploads/';
            $target = $folder . time().'_'.basename($new_image['name']);
            move_uploaded_file($new_image['tmp_name'], $target);
            $user_image = $target;
        }

        $sql = "UPDATE task_user SET
                user_first_name = :fn,
                user_last_name  = :ln,
                user_email_address = :mail,
                user_contact_no = :tel,
                user_date_of_birth = :dob,
                user_gender = :gender,
                user_address = :addr,
                user_image = :img,
                user_updated_on = NOW()
                WHERE user_id = :id";
        $pdo->prepare($sql)->execute([
            'fn'=>$user_first_name, 'ln'=>$user_last_name,
            'mail'=>$user_email_address, 'tel'=>$user_contact_no,
            'dob'=>$user_date_of_birth, 'gender'=>$user_gender,
            'addr'=>$user_address, 'img'=>$user_image,
            'id'=>$user_id
        ]);
        $success = true;
    } else {
        $message = '<ul><li>'.implode('</li><li>',$errors).'</li></ul>';
    }
}

include 'header.php';
?>

<h1 class="mt-4 text-primary fw-bold">Mon profil</h1>
<ol class="breadcrumb mb-4 bg-light rounded-3 p-3 shadow-sm">
    <li class="breadcrumb-item"><a href="task.php" class="text-primary text-decoration-none">Tâches</a></li>
    <li class="breadcrumb-item active text-secondary">Profil</li>
</ol>

<?php if ($message): ?>
    <div class="alert alert-danger shadow-sm"><?= $message ?></div>
<?php elseif ($success): ?>
    <div class="alert alert-success shadow-sm">Mise à jour réussie !</div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white"><b>Modifier mon profil</b></div>
    <div class="card-body">
        <form method="post" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="user_first_name" class="form-control" value="<?= htmlspecialchars($user_first_name) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nom</label>
                    <input type="text" name="user_last_name" class="form-control" value="<?= htmlspecialchars($user_last_name) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">E‑mail</label>
                    <input type="email" name="user_email_address" class="form-control" value="<?= htmlspecialchars($user_email_address) ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="user_contact_no" class="form-control" value="<?= htmlspecialchars($user_contact_no) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Genre</label>
                    <select name="user_gender" class="form-select" required>
                        <?php foreach (['Male'=>'Masculin','Female'=>'Féminin','Other'=>'Autre'] as $val=>$lib): ?>
                            <option value="<?= $val ?>" <?= $user_gender==$val?'selected':'' ?>><?= $lib ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date de naissance</label>
                    <input type="date" name="user_date_of_birth" class="form-control" value="<?= htmlspecialchars($user_date_of_birth) ?>" required>
                </div>

                <div class="col-md-8">
                    <label class="form-label">Adresse</label>
                    <input type="text" name="user_address" class="form-control" value="<?= htmlspecialchars($user_address) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Photo</label>
                    <input type="file" name="user_image" accept="image/*" class="form-control">
                    <?php if ($user_image): ?>
                        <img src="<?= htmlspecialchars($user_image) ?>" class="mt-2 rounded shadow-sm" style="width:100px;height:100px;object-fit:cover">
                    <?php endif; ?>
                </div>
            </div>

            <div class="text-center mt-4">
                <button class="btn btn-primary px-4 shadow-sm">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
