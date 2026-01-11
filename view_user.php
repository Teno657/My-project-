<?php

require_once 'db_connect.php';
require_once 'auth_function.php';

checkAdminLogin();
$message = '';
// Fetch user details if id is set
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT u.*, d.department_name FROM task_user u JOIN task_department d ON u.department_id = d.department_id WHERE u.user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $message = "User not found!";
    }
} else {
    $message = "Invalid user ID!";
}

include('header.php');

?>

<h1 class="mt-4">Gestion des stagiares</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="user.php">Gestion des stagiares</a></li>
    <li class="breadcrumb-item active">Details du stagiaire</li>
</ol>
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Details du stagiaire</div>
            <div class="card-body">
                <?php
                if($message !== ''){
                    echo '<div class="alert alert-danger">'.$message.'</div>';
                } else {
                ?>
                <div class="text-center mb-3">
                    <img src="<?php echo htmlspecialchars($user['user_image']); ?>" alt="User Image" class="rounded-circle img-thumbnail" width="100">
                </div>
                <?php
                }
                ?>
                <table class="table">
                <tr>
                    <th>Stagiaire ID</th>
                    <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                </tr>
                <tr>
                    <th>Nom</th>
                    <td><?php echo htmlspecialchars($user['user_first_name']); ?></td>
                </tr>
                <tr>
                    <th>Prenom</th>
                    <td><?php echo htmlspecialchars($user['user_last_name']); ?></td>
                </tr>
                <tr>
                    <th>Departement</th>
                    <td><?php echo htmlspecialchars($user['department_name']); ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo htmlspecialchars($user['user_email_address']); ?></td>
                </tr>
                <tr>
                    <th>Contact</th>
                    <td><?php echo htmlspecialchars($user['user_contact_no']); ?></td>
                </tr>
                <tr>
                    <th>Date de naissance</th>
                    <td><?php echo htmlspecialchars($user['user_date_of_birth']); ?></td>
                </tr>
                <tr>
                    <th>Genre</th>
                    <td><?php echo htmlspecialchars($user['user_gender']); ?></td>
                </tr>
                <tr>
                    <th>Adresse</th>
                    <td><?php echo htmlspecialchars($user['user_address']); ?></td>
                </tr>
                <tr>
                    <th>Statut</th>
                    <td><?php echo ($user['user_status'] === 'Enable') ? '<span class="badge bg-success">Enable</span>' : '<span class="badge bg-danger">Disable</span>'; ?></td>
                </tr>
                <tr>
                    <th>Ajouter le</th>
                    <td><?php echo htmlspecialchars($user['user_added_on']); ?></td>
                </tr>
                <tr>
                    <th>Mis a jour le</th>
                    <td><?php echo htmlspecialchars($user['user_updated_on']); ?></td>
                </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Details de la tache</div>
            <div class="card-body">
                <table id="taskTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Departement</th>
                            <th>Titre de la tache</th>
                            <th>Date-Debut</th>
                            <th>Date-Debut</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
include('footer.php');
?>

<script>
$(document).ready(function() {
    $('#taskTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "task_ajax.php?user_id=<?php echo $user_id; ?>",
            "type": "GET"
        },
        "columns": [
            { "data": "task_id" },
            { "data": "department_name" },
            { "data": "task_title" },
            { "data": "task_assign_date" },
            { "data": "task_end_date" },
            { 
                "data" : null,
                "render" : function(data, type, row){
                    if(row.task_status === 'Pending'){
                        return `<span class="badge bg-primary">En attente</span>`;
                    }
                    if(row.task_status === 'Viewed'){
                        return `<span class="badge bg-info">Vue</span>`;
                    }
                    if(row.task_status === 'In Progress'){
                        return `<span class="badge bg-warning">En cours</span>`;
                    }
                    if(row.task_status === 'Completed'){
                        return `<span class="badge bg-success">Acconplie</span>`;
                    }
                    if(row.task_status === 'Delayed'){
                        return `<span class="badge bg-danger">En retard</span>`;
                    }
                } 
            },
            {
                "data" : null,
                "render" : function(data, type, row){
                    let btn = `<a href="view_task.php?id=${row.task_id}" class="btn btn-primary btn-sm">Vue</a>&nbsp;`;
                    <?php
                    if(isset($_SESSION["admin_logged_in"])){
                    ?>
                    if(row.task_status === 'Pending'){
                        btn += `<a href="edit_task.php?id=${row.task_id}" class="btn btn-warning btn-sm">Modifier</a>&nbsp;`;
                        btn += `<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="${row.task_id}">Supprimer</button>`;
                    }
                    <?php
                    }
                    ?>
                    return `
                    <div class="text-center">
                        ${btn}
                    </div>
                    `;
                }
            }
        ]
    });
});
</script>