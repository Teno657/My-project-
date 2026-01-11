<?php
date_default_timezone_set('Africa/Douala');

require_once 'db_connect.php';
require_once 'auth_function.php';

checkAdminLogin();

$message = '';

// Récupérer les détails de la tâche si ID fourni
if (isset($_GET['id'])) {
    $task_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM task_manage WHERE task_id = ?");
    $stmt->execute([$task_id]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$task) {
        $message = "Tâche introuvable !";
        exit;
    }

    $departments = $pdo->query("SELECT department_id, department_name FROM task_department WHERE department_status = 'Enable'")->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT user_id, CONCAT(user_first_name, ' ', user_last_name) AS user_name FROM task_user WHERE department_id = ? AND user_status = 'enable'");
    $stmt->execute([$task['task_department_id']]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $message = "ID de tâche invalide !";
    exit;
}

// Traitement du formulaire POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $task_id = $_POST['task_id'];
    $task_department_id = $_POST['task_department_id'];
    $task_user_to = $_POST['task_user_to'];
    $task_title = trim($_POST['task_title']);
    $task_creator_description = trim($_POST['task_creator_description']);
    $task_assign_date = $_POST['task_assign_date'];
    $task_end_date = $_POST['task_end_date'];

    if (empty($task_department_id)) $errors[] = 'Le département est requis.';
    if (empty($task_user_to)) $errors[] = 'Le stagiaire est requis.';
    if (empty($task_title)) $errors[] = 'Le titre de la tâche est requis.';
    if (empty($task_creator_description)) $errors[] = 'La description de la tâche est requise.';
    if (empty($task_assign_date)) $errors[] = 'La date d\'assignation est requise.';
    if (empty($task_end_date)) $errors[] = 'La date de fin est requise.';

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM task_manage WHERE task_title = ? AND task_id != ?");
    $stmt->execute([$task_title, $task_id]);
    if ($stmt->fetchColumn()) $errors[] = 'Ce titre de tâche existe déjà.';

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE task_manage SET task_department_id = ?, task_user_to = ?, task_title = ?, task_creator_description = ?, task_assign_date = ?, task_end_date = ?, task_status = 'Pending', task_updated_on = NOW() WHERE task_id = ?");
        $stmt->execute([$task_department_id, $task_user_to, $task_title, $task_creator_description, $task_assign_date, $task_end_date, $task_id]);

        // ✅ Notification pour le stagiaire
        $notifMessage = "La tâche <span style='color:#0d6efd; font-weight:bold;'>"
                      . htmlspecialchars($task_title)
                      . "</span> a été modifiée par l’administrateur le " . date('d/m/Y H:i');
        $notifLink = "view_task.php?id=" . $task_id;

        $notifStmt = $pdo->prepare("INSERT INTO user_notifications (user_id, message, link) VALUES (?, ?, ?)");
        $notifStmt->execute([$task_user_to, $notifMessage, $notifLink]);

       // Stocker message succès dans session pour affichage sur task.php
$_SESSION['success_message'] = "La tâche a été modifiée avec succès.";
$_SESSION['message_origin'] = 'task';

header("Location: task.php");
exit;

    } else {
        $message = '<ul class="list-unstyled">';
        foreach ($errors as $error) {
            $message .= '<li>' . htmlspecialchars($error) . '</li>';
        }
        $message .= '</ul>';
    }
}

include('header.php');
?>

<h1 class="mt-4">Modifier la tâche</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="task.php">Gestion des tâches</a></li>
    <li class="breadcrumb-item active">Modifier la tâche</li>
</ol>

<?php if ($message !== ''): ?>
    <div class="alert alert-danger"><?= $message ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Modifier la tâche</div>
    <div class="card-body">
        <form id="editTaskForm" method="POST" action="edit_task.php?id=<?= htmlspecialchars($task_id) ?>">
            <input type="hidden" name="task_id" value="<?= htmlspecialchars($task_id) ?>">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="task_department_id">Nom du département</label>
                    <select name="task_department_id" id="task_department_id" class="form-select" required>
                        <option value="">Sélectionner un département</option>
                        <?php foreach ($departments as $department): ?>
                            <option value="<?= $department['department_id'] ?>" <?= $department['department_id'] == $task['task_department_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($department['department_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="task_user_to">Nom du stagiaire</label>
                    <select name="task_user_to" id="task_user_to" class="form-select" required>
                        <option value="">Sélectionner un stagiaire</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['user_id'] ?>" <?= $user['user_id'] == $task['task_user_to'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['user_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="task_title">Titre de la tâche</label>
                <input type="text" name="task_title" id="task_title" class="form-control" value="<?= htmlspecialchars($task['task_title']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="task_creator_description">Description de la tâche</label>
                <textarea name="task_creator_description" id="task_creator_description" class="summernote" required><?= htmlspecialchars($task['task_creator_description']) ?></textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="task_assign_date">Date d'assignation de la tâche</label>
                    <input type="date" name="task_assign_date" id="task_assign_date" class="form-control" value="<?= htmlspecialchars($task['task_assign_date']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="task_end_date">Date de fin de la tâche</label>
                    <input type="date" name="task_end_date" id="task_end_date" class="form-control" value="<?= htmlspecialchars($task['task_end_date']) ?>" required>
                </div>
            </div>

            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-primary">Modifier</button>
            </div>
        </form>
    </div>
</div>

<?php include('footer.php'); ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>

<script>
$(document).ready(function() {
    $('.summernote').summernote({ height: 200 });

    $('#task_department_id').change(function() {
        var departmentId = $(this).val();
        if (departmentId) {
            $.post('fetch_users.php', { department_id: departmentId }, function(data) {
                $('#task_user_to').html(data);
            });
        } else {
            $('#task_user_to').html('<option value="">Sélectionner un stagiaire</option>');
        }
    });
});
</script>
