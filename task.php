<?php
session_start();

require_once 'db_connect.php';
require_once 'auth_function.php';

checkAdminOrUserLogin();

include('header.php');
?>

<?php if (isset($_SESSION['success_message']) && ($_SESSION['message_origin'] ?? '') === 'task'): ?>
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <?= htmlspecialchars($_SESSION['success_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    <?php
    // Supprimer le message pour ne pas le réafficher au rafraîchissement
    unset($_SESSION['success_message'], $_SESSION['message_origin']);
    ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message']) && ($_SESSION['message_origin'] ?? '') === 'task'): ?>
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <?= htmlspecialchars($_SESSION['error_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    <?php
    unset($_SESSION['error_message'], $_SESSION['message_origin']);
    ?>
<?php endif; ?>

<h1 class="mt-4 mb-3 text-primary fw-bold">
    <?= isset($_SESSION['admin_logged_in']) ? 'Gestion des tâches' : 'STAGE MANAGER 📊'; ?>
</h1>

<ol class="breadcrumb mb-4 bg-light rounded-3 p-3 shadow-sm">
    <?php if (isset($_SESSION['admin_logged_in'])): ?>
        <li class="breadcrumb-item"><a href="dashboard.php" class="text-decoration-none text-primary">Dashboard</a></li>
    <?php endif; ?>
    <li class="breadcrumb-item active text-secondary">
        <?= isset($_SESSION['admin_logged_in']) ? 'Gestion des tâches' : 'Consultez vos tâches'; ?>
    </li>
</ol>

<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Liste des tâches</h5>
        <?php if (isset($_SESSION["admin_logged_in"])): ?>
            <a href="add_task.php" class="btn btn-success btn-sm shadow-sm">
                <i class="fas fa-plus-circle me-1"></i> Ajouter
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="taskTable" class="table table-striped table-hover align-middle mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Département</th>
                        <th>Détail du stagiaire</th>
                        <th>Titre de la tâche</th>
                        <th>Date-Debut</th>
                        <th>Date-Fin</th>
                        <th>Statut</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#taskTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "task_ajax.php",
            type: "GET",
            data: function(d) {
                d.user_id = <?= json_encode($_SESSION['user_id'] ?? null) ?>;
            }
        },
        columns: [
            { data: "task_id" },
            { data: "department_name" },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `<img src="${row.user_image}" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;" alt="User Image" />${row.user_first_name} ${row.user_last_name}`;
                }
            },
            { data: "task_title" },
            { data: "task_assign_date" },
            { data: "task_end_date" },
            {
                data: null,
                render: function(data, type, row) {
                    const statusMap = {
                        "Pending": {text: "En attente", class: "bg-primary"},
                        "Viewed": {text: "Vue", class: "bg-info"},
                        "In Progress": {text: "En cours", class: "bg-warning"},
                        "Completed": {text: "Accomplie", class: "bg-success"},
                        "Delayed": {text: "En retard", class: "bg-danger"}
                    };
                    const status = statusMap[row.task_status] || {text: row.task_status, class: "bg-secondary"};
                    return `<span class="badge ${status.class} shadow-sm" style="font-size: 0.9rem;">${status.text}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                className: "text-center",
                render: function(data, type, row) {
                    let btns = `<a href="view_task.php?id=${row.task_id}" class="btn btn-primary btn-sm shadow-sm me-1" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>`;

                    if (<?= isset($_SESSION["admin_logged_in"]) ? 'true' : 'false' ?>) {
                        btns += `<a href="edit_task.php?id=${row.task_id}" class="btn btn-warning btn-sm shadow-sm me-1" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>`;
                        btns += `<button type="button" class="btn btn-danger btn-sm shadow-sm btn-delete" data-id="${row.task_id}" title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>`;
                    }
                    return `<div class="d-flex justify-content-center">${btns}</div>`;
                }
            }
        ],
        language: {
            processing: "Chargement...",
            search: "Recherche :",
            lengthMenu: "Afficher _MENU_ enregistrements",
            info: "Affichage de _START_ à _END_ sur _TOTAL_",
            infoEmpty: "Aucun enregistrement disponible",
            infoFiltered: "(filtré de _MAX_ enregistrements au total)",
            paginate: {
                first: "Premier",
                last: "Dernier",
                next: "Suivant",
                previous: "Précédent"
            }
        }
    });

    // Suppression avec redirection
    $(document).on('click', '.btn-delete', function() {
        const taskId = $(this).data('id');
            window.location.href = 'prepare_delete_task.php?id=' + taskId;
    });
});
</script>
<?php if (isset($_SESSION['delete_task_pending'])): 
    $pendingTaskId = $_SESSION['delete_task_pending']['task_id'];
?>
<!-- Modal de confirmation suppression -->
<div class="modal fade show" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" style="display:block; background-color: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="delete_task.php">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmDeleteLabel">Confirmation suppression</h5>
        </div>
        <div class="modal-body">
          <p>Voulez-vous vraiment supprimer cette tâche ?</p>
          <input type="hidden" name="task_id" value="<?= htmlspecialchars($pendingTaskId) ?>">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Oui, supprimer</button>
          <a href="cancel_delete_task.php" class="btn btn-secondary">Annuler</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Bloquer scroll quand modal visible
  document.body.style.overflow = 'hidden';
</script>
<?php endif; ?>
