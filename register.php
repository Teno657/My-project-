<?php
require_once 'db_connect.php';

$message = '';

// Charger les départements actifs
$departments = $pdo->query("SELECT department_id, department_name FROM task_department WHERE department_status = 'Enable'")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['user_first_name']);
    $last_name = trim($_POST['user_last_name']);
    $email = trim($_POST['user_email_address']);
    $password = trim($_POST['user_email_password']);
    $contact = trim($_POST['user_contact_no']);
    $dob = trim($_POST['user_date_of_birth']);
    $gender = trim($_POST['user_gender']);
    $address = trim($_POST['user_address']);
    $department_id = $_POST['department_id'] ?? null;
    $status = 'Disable';
    $image = $_FILES['user_image'];
    $cv = $_FILES['cv_file'];
$motivation = $_FILES['motivation_letter'];
$identity = $_FILES['identity_doc'];


    if (
        empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($contact) ||
        empty($dob) || empty($gender) || empty($address) || empty($department_id)
    ) {
        $message = 'Tous les champs sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Adresse email invalide.';
} elseif (
    $image['error'] !== UPLOAD_ERR_OK ||
    $cv['error'] !== UPLOAD_ERR_OK ||
    $motivation['error'] !== UPLOAD_ERR_OK ||
    $identity['error'] !== UPLOAD_ERR_OK
) {
    $message = 'Erreur lors du téléchargement des fichiers (photo ou documents).';

    } else {
        // Vérifier si email déjà utilisé
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM task_user WHERE user_email_address = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            $message = 'Cet email est déjà utilisé.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $image_name = uniqid('user_', true) . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
            $image_path = 'uploads/' . $image_name;
            $cv_name = uniqid('cv_', true) . '.pdf';
$cv_path = 'uploads/docs/' . $cv_name;

$motivation_name = uniqid('motivation_', true) . '.pdf';
$motivation_path = 'uploads/docs/' . $motivation_name;

$identity_ext = pathinfo($identity['name'], PATHINFO_EXTENSION);
$identity_name = uniqid('identity_', true) . '.' . $identity_ext;
$identity_path = 'uploads/docs/' . $identity_name;


           if (
    move_uploaded_file($image['tmp_name'], $image_path) &&
    move_uploaded_file($cv['tmp_name'], $cv_path) &&
    move_uploaded_file($motivation['tmp_name'], $motivation_path) &&
    move_uploaded_file($identity['tmp_name'], $identity_path)
) {

                try {
                    $stmt = $pdo->prepare("INSERT INTO task_user 
                        (user_first_name, user_last_name, department_id, user_email_address, user_email_password, user_contact_no, user_date_of_birth, user_gender, user_address, user_status, user_image,cv_path, motivation_letter_path, identity_doc_path,
 user_added_on, user_updated_on) 
                        VALUES 
                        (:first_name, :last_name, :department_id, :email, :password, :contact, :dob, :gender, :address, :status, :image, :cv_path, :motivation_letter_path, :identity_doc_path,
NOW(), NOW())");

                    $stmt->execute([
                        'first_name' => $first_name,
                        'last_name'  => $last_name,
                        'department_id' => $department_id,
                        'email'      => $email,
                        'password'   => $hashed_password,
                        'contact'    => $contact,
                        'dob'        => $dob,
                        'gender'     => $gender,
                        'address'    => $address,
                        'status'     => $status,
                        'image'      => $image_path,
                        'cv_path' => $cv_path,
                       'motivation_letter_path' => $motivation_path,
                        'identity_doc_path' => $identity_path,

                    ]);

                    // Gestion fuseau horaire Africa/Douala (UTC+1)
                    $now = new DateTime("now", new DateTimeZone('UTC'));
                    $now->setTimezone(new DateTimeZone('Africa/Douala'));
                    $formattedDate = $now->format('d/m/Y H:i');

                    $notifMessage = "Nouvelle inscription de stagiaire : $first_name $last_name\n"
                                  . "Email : $email\n"
                                  . "Inscrit le : $formattedDate";

                    // Insérer la notification
                    $notifStmt = $pdo->prepare("INSERT INTO notifications (message, created_at, is_read) VALUES (:message, NOW(), 0)");
                    $notifStmt->execute(['message' => $notifMessage]);

                    $message = 'Inscription réussie. En attente de validation par l\'administrateur.';

                } catch (PDOException $e) {
                    $message = 'Erreur base de données : ' . $e->getMessage();
                    if (file_exists($image_path)) unlink($image_path);
                }
            } else {
                $message = 'Erreur lors de l’enregistrement de la photo.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Inscription Stagiaire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
            max-width: 750px;
            width: 100%;
            padding: 30px;
        }
        label { font-weight: 600; }
        input, textarea, select {
            border-radius: 8px;
            border: 2px solid #ddd;
            padding: 10px;
            font-size: 1rem;
            background-color: #f9f9f9;
        }
        input:focus, textarea:focus, select:focus {
            border-color: #198754;
            background-color: #fff;
            box-shadow: 0 0 8px rgba(25, 135, 84, 0.4);
        }
        .btn-success {
            background-color: #198754;
            padding: 12px 25px;
            border-radius: 50px;
            width: 100%;
            max-width: 250px;
            margin: 0 auto;
            display: block;
        }
        .btn-success:hover {
            background-color: #145c36;
            box-shadow: 0 0 10px rgba(20, 92, 54, 0.6);
        }
    </style>
</head>
<body>
    <div class="card">
        <h3 class="text-center mb-4 text-success">Inscription Stagiaire</h3>

        <?php if ($message): ?>
            <div class="alert alert-success text-center"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="user_first_name">Nom :</label>
                    <input type="text" id="user_first_name" name="user_first_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="user_last_name">Prénom :</label>
                    <input type="text" id="user_last_name" name="user_last_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="user_email_address">Email :</label>
                    <input type="email" id="user_email_address" name="user_email_address" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="user_email_password">Mot de passe :</label>
                    <input type="password" id="user_email_password" name="user_email_password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="user_contact_no">Contact :</label>
                    <input type="text" id="user_contact_no" name="user_contact_no" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="user_date_of_birth">Date de naissance :</label>
                    <input type="date" id="user_date_of_birth" name="user_date_of_birth" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="user_gender">Genre :</label>
                    <select id="user_gender" name="user_gender" class="form-select" required>
                        <option value="">-- Choisir --</option>
                        <option value="Male">Masculin</option>
                        <option value="Female">Féminin</option>
                        <option value="Other">Autre</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="department_id">Département :</label>
                    <select id="department_id" name="department_id" class="form-select" required>
                        <option value="">-- Choisissez un département --</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['department_id'] ?>"><?= htmlspecialchars($dept['department_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label for="user_address">Adresse :</label>
                    <textarea id="user_address" name="user_address" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-12">
                    <label for="user_image">Photo de profil :</label>
                    <input type="file" id="user_image" name="user_image" class="form-control" accept="image/*" required>
                </div>
                <div class="col-md-4">
    <label for="cv_file">CV (PDF) :</label>
    <input type="file" id="cv_file" name="cv_file" class="form-control" accept=".pdf" required>
</div>
<div class="col-md-4">
    <label for="motivation_letter">Lettre de motivation (PDF) :</label>
    <input type="file" id="motivation_letter" name="motivation_letter" class="form-control" accept=".pdf" required>
</div>
<div class="col-md-4">
    <label for="identity_doc">Pièce d'identité (PDF ou image) :</label>
    <input type="file" id="identity_doc" name="identity_doc" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
</div>

            </div>
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">S'inscrire</button>
            </div>
        </form>
    </div>
</body>
</html>
