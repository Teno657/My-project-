<?php
session_start();
require_once 'db_connect.php';
require_once 'auth_function.php';
checkAdminLogin();

include('header.php');
?>

<?php if (isset($_SESSION['success_message']) && ($_SESSION['message_origin'] ?? '') === 'user'): ?>
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <?= htmlspecialchars($_SESSION['success_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    <?php unset($_SESSION['success_message'], $_SESSION['message_origin']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message']) && ($_SESSION['message_origin'] ?? '') === 'user'): ?>
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <?= htmlspecialchars($_SESSION['error_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    <?php unset($_SESSION['error_message'], $_SESSION['message_origin']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['pending_delete_user'])): ?>
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
    .delete-box h5 {
        color: #d63384;
        margin-bottom: 1rem;
    }
    .delete-box .btn {
        margin-top: 1.5rem;
    }
</style>

<div id="deleteOverlay">
    <div class="delete-box">
        <h5>Suppression du stagiaire en cours...</h5>
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
            fetch('confirm_delete_user.php?id=<?= $_SESSION['pending_delete_user']['user_id'] ?>')
                .then(res => res.json())
               .then(res => {
    if (res.success) {
        window.location.reload();
    } else {
        alert('Erreur lors de la suppression : ' + (res.message || 'Erreur inconnue'));
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
        fetch('cancel_delete_user.php')
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
<?php unset($_SESSION['pending_delete_user']); ?>
<?php endif; ?>

<h1 class="mt-4 mb-3 text-primary fw-bold">Gestion des stagiaires</h1>
<ol class="breadcrumb mb-4 bg-light rounded-3 p-3 shadow-sm">
    <li class="breadcrumb-item"><a href="dashboard.php" class="text-decoration-none text-primary">Dashboard</a></li>
    <li class="breadcrumb-item active text-secondary">Gestion des stagiaires</li>
</ol>

<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Liste des stagiaires</h5>
        <a href="add_user.php" class="btn btn-success btn-sm shadow-sm">
            <i class="fas fa-user-plus me-1"></i> Ajouter
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="userTable" class="table table-striped table-hover align-middle mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>Image</th>
                        <th>ID</th>
                        <th>Département</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Contact</th>
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

<script>
$(document).ready(function() {
    $('#userTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "user_ajax.php",
            "type": "GET"
        },
        "columns": [
            {
                "data": null,
                "orderable": false,
                "render": function(data, type, row) {
                    return `<img src="${row.user_image}" class="rounded-circle" width="50" height="50" style="object-fit: cover;" />`;
                }
            },
            { "data": "user_id" },
            { "data": "department_name" },
            { "data": "user_first_name" },
            { "data": "user_last_name" },
            { "data": "user_email_address" },
            { "data": "user_contact_no" },
            { 
                "data": null,
                "render": function(data, type, row) {
                    return row.user_status === 'Enable' 
                        ? '<span class="badge bg-success">Activé</span>' 
                        : '<span class="badge bg-danger">Désactivé</span>';
                }
            },
            {
                "data": null,
                "orderable": false,
                "className": "text-center",
                "render": function(data, type, row) {
                 return `
    <div class="d-flex justify-content-center gap-2">
        <a href="view_user.php?id=${row.user_id}" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a>
        <a href="edit_user.php?id=${row.user_id}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
        <a href="download_documents.php?id=${row.user_id}" class="btn btn-info btn-sm" title="Télécharger documents">
            <i class="fas fa-paperclip"></i>
        </a>
        <button class="btn btn-danger btn-sm btn-delete" data-id="${row.user_id}"><i class="fas fa-trash"></i></button>
    </div>
`;

                }
            }
        ]
    });

    $(document).on('click', '.btn-delete', function() {
        const userId = $(this).data('id');
        window.location.href = 'prepare_delete_user.php?id=' + userId;
    });
});
</script>
