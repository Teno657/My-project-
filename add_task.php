<?php
require_once 'db_connect.php';
require_once 'auth_function.php';

checkAdminLogin();

// Récupération des départements
$departments = $pdo->query("SELECT department_id, department_name FROM task_department WHERE department_status = 'enable'")
                  ->fetchAll(PDO::FETCH_ASSOC);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];

    $task_department_id = $_POST['task_department_id'];
    $task_user_to = $_POST['task_user_to'];
    $task_title = trim($_POST['task_title']);
    $task_creator_description = trim($_POST['task_creator_description']);
    $task_assign_date = $_POST['task_assign_date']; // Date générée automatiquement
    $task_end_date = $_POST['task_end_date'];

    // Validation
    if (empty($task_department_id)) $errors[] = 'Le département est obligatoire.';
    if (empty($task_user_to)) $errors[] = 'Le stagiaire est obligatoire.';
    if (empty($task_title)) $errors[] = 'Le titre de la tâche est obligatoire.';
    if (empty($task_creator_description)) $errors[] = 'La description est obligatoire.';
    if (empty($task_assign_date)) $errors[] = 'La date d\'attribution est obligatoire.';
    if (empty($task_end_date)) $errors[] = 'La date de fin est obligatoire.';

    if (empty($errors)) {
        // Insertion tâche
        $stmt = $pdo->prepare("INSERT INTO task_manage 
            (task_title, task_creator_description, task_department_id, task_user_to, task_assign_date, task_end_date, task_status, task_added_on, task_updated_on) 
            VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW(), NOW())");
        $stmt->execute([$task_title, $task_creator_description, $task_department_id, $task_user_to, $task_assign_date, $task_end_date]);

        // ✅ Notification
        $insertedTaskId = $pdo->lastInsertId();
        $notif_message = "Nouvelle tâche assignée : " . $task_title;
        $notif_link = "view_task.php?id=" . $insertedTaskId;

        $notifStmt = $pdo->prepare("INSERT INTO user_notifications (user_id, message, link) VALUES (?, ?, ?)");
      $notifStmt->execute([$task_user_to, $notif_message, $notif_link]);

// ✅ Ajout du message de succès dans la session
session_start(); // Important si pas déjà au tout début du fichier
$_SESSION['success_message'] = "Tâche ajoutée avec succès.";
$_SESSION['message_origin'] = 'task';

// Redirection
header("Location: task.php");
exit;


    } else {
        $message = '<ul class="list-unstyled">';
        foreach ($errors as $error) {
            $message .= '<li>' . $error . '</li>';
        }
        $message .= '</ul>';
    }
}

include('header.php');
?>

<h1 class="mt-4">Ajouter une tâche</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="task.php">Task Management</a></li>
    <li class="breadcrumb-item active">Ajouter une tâche</li>
</ol>

<?php if ($message !== ''): ?>
    <div class="alert alert-danger"><?= $message ?></div>
<?php endif; ?>

<form id="addTaskForm" method="POST" action="add_task.php">

    <!-- Bloc 1 -->
    <div class="card mb-4">
        <div class="card-header">Informations du stagiaire</div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="task_department_id" class="form-label">Nom du département</label>
                    <select name="task_department_id" id="task_department_id" class="form-select">
                        <option value="">Sélectionner un département</option>
                        <?php foreach ($departments as $department): ?>
                            <option value="<?= htmlspecialchars($department['department_id']) ?>">
                                <?= htmlspecialchars($department['department_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="task_user_to" class="form-label">Nom du stagiaire</label>
                    <select name="task_user_to" id="task_user_to" class="form-select">
                        <option value="">Sélectionner un stagiaire</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Bloc 2 -->
    <div class="card mb-4">
        <div class="card-header">Informations de la tâche</div>
        <div class="card-body">
            <div class="mb-3">
                <label for="task_title" class="form-label">Titre de la tâche</label>
                <input type="text" name="task_title" id="task_title" class="form-control">
            </div>
            <div class="mb-3">
                <label for="task_creator_description" class="form-label">Description de la tâche</label>
                <textarea name="task_creator_description" id="task_creator_description" class="summernote"></textarea>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="task_assign_date" class="form-label">Date d'attribution</label>
                    <input type="date" name="task_assign_date" id="task_assign_date" class="form-control" readonly>
                </div>
                <div class="col-md-6">
                    <label for="task_end_date" class="form-label">Date de fin</label>
                    <input type="date" name="task_end_date" id="task_end_date" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mb-5">
        <button type="submit" class="btn btn-primary">Ajouter la tâche</button>
    </div>
</form>

<?php include('footer.php'); ?>

<!-- Summernote pour la description -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>

<script>
$(document).ready(function () {
    $('.summernote').summernote({ height: 200 });

    $('#task_department_id').change(function () {
        var departmentId = $(this).val();
        if (departmentId) {
            $.ajax({
                url: 'fetch_users.php',
                type: 'POST',
                data: { department_id: departmentId },
                success: function (data) {
                    $('#task_user_to').html(data);
                }
            });
        } else {
            $('#task_user_to').html('<option value="">Sélectionner un stagiaire</option>');
        }
    });
});
</script>

<!-- Script pour remplir la date d’attribution automatiquement -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    const formattedDate = `${yyyy}-${mm}-${dd}`;
    document.getElementById('task_assign_date').value = formattedDate;
});
</script>
