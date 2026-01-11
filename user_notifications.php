<?php
require_once 'db_connect.php';
require_once 'auth_function.php';
checkUserLogin();

$user_id = $_SESSION['user_id'];

// Suppression de la notification demandée
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    // Sécurité : vérifier que la notif appartient bien à l'utilisateur
    $stmt = $pdo->prepare("DELETE FROM user_notifications WHERE id = ? AND user_id = ?");
    $stmt->execute([$delete_id, $user_id]);
    header("Location: user_notifications.php");
    exit;
}

// Marquer comme lues les notifications si demandé
if (isset($_GET['mark']) && $_GET['mark'] === 'read') {
    $pdo->prepare("UPDATE user_notifications SET is_read = 1 WHERE user_id = ?")->execute([$user_id]);
    header("Location: user_notifications.php");
    exit;
}

// Récupération des notifications
$stmt = $pdo->prepare("SELECT * FROM user_notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<div class="container mt-5">
    <h2 class="mb-4">Vos notifications</h2>

    <a href="?mark=read" class="btn btn-sm btn-outline-success mb-3">Marquer tout comme lu</a>
    <a href="task.php" class="btn btn-sm btn-primary mb-4 ms-2">Retour</a>

    <?php if (count($notifications) > 0): ?>
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Messages</th>
                    <th>Date &amp; Heure</th>
                    <th>Lien</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notifications as $notif): ?>
                    <tr class="<?= $notif['is_read'] ? '' : 'fw-bold' ?>">
                        <td><?= $notif['message'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?></td>
                        <td>
                        <button onclick="window.location.href='<?= htmlspecialchars($notif['link']) ?>'" class="btn btn-sm btn-outline-primary">
    Voir
</button>

                        </td>
                        <td>
                            <a href="?delete_id=<?= $notif['id'] ?>" class="btn btn-sm btn-danger">
                                Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucune notification.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
