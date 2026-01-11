<?php
session_start();
require_once 'db_connect.php';

// Vérifier que l'admin est connecté (tu peux adapter)
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

// Récupérer toutes les notifications
$stmt = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC");
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Toutes les notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="p-4">

    <h1>Toutes les notifications</h1>

    <?php if (empty($notifications)): ?>
        <p>Aucune notification disponible.</p>
    <?php else: ?>
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Messages</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notifications as $notif): ?>
                    <tr>
                        <td><?= nl2br(htmlspecialchars($notif['message'])) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?></td>
                        <td>
                            <?php if (!$notif['is_read']): ?>
                                <span class="badge bg-warning text-dark">Non lue</span>
                            <?php else: ?>
                                <span class="badge bg-success">Lue</span>
                            <?php endif; ?>
                        </td>
                        <td class="d-flex gap-2">
                            <?php if (!$notif['is_read']): ?>
                                <form method="POST" action="mark_notification_read.php" style="margin:0;">
                                    <input type="hidden" name="notification_id" value="<?= (int)$notif['id'] ?>">
                                    <button type="submit" class="btn btn-outline-success btn-sm" title="Marquer comme lu">
                                        <i class="fas fa-check"></i> Marquer comme lu
                                    </button>
                                </form>
                            <?php endif; ?>

                            <form method="POST" action="delete_notification.php" style="margin:0;">
                                <input type="hidden" name="notification_id" value="<?= (int)$notif['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm" title="Supprimer">
                                    <i class="fas fa-trash-alt"></i> Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-secondary mt-3">Retour au tableau de bord</a>

</body>
</html>
