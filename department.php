<?php
require_once 'db_connect.php';
require_once 'auth_function.php';

checkAdminLogin();
include('header.php');
?>

<?php if (isset($_SESSION['success_message']) && ($_SESSION['message_origin'] ?? '') === 'department'): ?>
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <?= htmlspecialchars($_SESSION['success_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    <?php unset($_SESSION['success_message'], $_SESSION['message_origin']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message']) && ($_SESSION['message_origin'] ?? '') === 'department'): ?>
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <?= htmlspecialchars($_SESSION['error_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    <?php unset($_SESSION['error_message'], $_SESSION['message_origin']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['pending_delete_department'])): ?>
<style>
#deleteOverlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}
.delete-box {
    background: #fff;
    border-radius: 15px;
    padding: 2rem 3rem;
    text-align: center;
    box-shadow: 0 0 30px rgba(0,0,0,0.3);
    max-width: 400px;
    animation: popIn 0.5s ease;
}
@keyframes popIn {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
</style>

<div id="deleteOverlay">
    <div class="delete-box">
        <h5 class="text-danger">Suppression du département en cours...</h5>
        <p>La suppression commencera dans <strong><span id="countdown">5</span></strong> secondes</p>
        <button class="btn btn-outline-secondary" id="cancelDeleteBtn">Annuler</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let secondsLeft = 5;
    const countdownEl = document.getElementById('countdown');
    const cancelBtn = document.getElementById('cancelDeleteBtn');
    const overlay = document.getElementById('deleteOverlay');

    const intervalId = setInterval(() => {
        secondsLeft--;
        countdownEl.textContent = secondsLeft;
        if (secondsLeft <= 0) {
            clearInterval(intervalId);
            fetch('confirm_delete_department.php?id=<?= $_SESSION['pending_delete_department']['department_id'] ?>')
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        window.location.reload();
                    } else {
                        alert('Erreur lors de la suppression.');
                        overlay.remove();
                    }
                })
                .catch(() => {
                    alert('Erreur de connexion.');
                    overlay.remove();
                });
        }
    }, 1000);

    cancelBtn.addEventListener('click', () => {
        clearInterval(intervalId);
        fetch('cancel_delete_department.php')
            .then(() => {
                overlay.remove();
                window.location.reload();
            })
            .catch(() => {
                alert("Erreur lors de l'annulation.");
            });
    });
});
</script>
<?php unset($_SESSION['pending_delete_department']); ?>
<?php endif; ?>

<h1 class="mt-4 mb-3 text-primary fw-bold">Gestion des départements</h1>
<ol class="breadcrumb mb-4 bg-light rounded-3 p-3 shadow-sm">
    <li class="breadcrumb-item"><a href="dashboard.php" class="text-decoration-none text-primary">Dashboard</a></li>
    <li class="breadcrumb-item active text-secondary">Gestion des départements</li>
</ol>

<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Liste des départements</h5>
        <a href="add_department.php" class="btn btn-success btn-sm shadow-sm">
           <i class="fas fa-plus me-1"></i> Ajouter
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="departmentTable" class="table table-striped table-hover align-middle mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Statut</th>
                        <th>Ajouté le</th>
                        <th>Mis à jour le</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

<script>
$(document).ready(function() {
    $('#departmentTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "department_ajax.php",
            type: "GET"
        },
        columns: [
            { data: "department_id" },
            { data: "department_name" },
            { 
                data : null,
                render : function(data, type, row){
                    if(row.department_status === 'Enable'){
                        return `<span class="badge bg-success shadow-sm" style="font-size:0.9rem;">Activé</span>`;
                    } else {
                        return `<span class="badge bg-danger shadow-sm" style="font-size:0.9rem;">Désactivé</span>`;
                    }
                } 
            },
            { data: "department_added_on" },
            { data: "department_updated_on" },
            {
                data : null,
                className: "text-center",
                orderable: false,
                render : function(data, type, row){
                    return `
                    <div class="d-flex justify-content-center gap-2">
                        <a href="edit_department.php?id=${row.department_id}" class="btn btn-warning btn-sm shadow-sm">
                           <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-danger btn-sm shadow-sm btn-delete" data-id="${row.department_id}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>`;
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
            },
        }
    });

    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        window.location.href = 'prepare_delete_department.php?id=' + id;
    });
});
</script>

<style>
.btn-warning:hover {
    background-color: #e0a800;
    box-shadow: 0 4px 12px rgba(224, 168, 0, 0.6);
}
.btn-danger:hover {
    background-color: #dc3545cc;
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.6);
}
.btn-success:hover {
    background-color: #198754cc;
    box-shadow: 0 4px 12px rgba(25, 135, 84, 0.6);
}
#departmentTable tbody tr:hover {
    background-color: #f1f5f9;
    transition: background-color 0.3s ease;
}
</style>
