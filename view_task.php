<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db_connect.php';
require_once 'auth_function.php';

// Maintenant tu peux utiliser $pdo en toute sécurité

if (isset($_GET['delete_reply_id']) && is_numeric($_GET['delete_reply_id'])) {
    $reply_id = (int) $_GET['delete_reply_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM task_comment_replies WHERE reply_id = ?");
        $stmt->execute([$reply_id]);

        header("Location: view_task.php?id=" . (int) $_GET['id'] . "&msg=deleted");
        exit;
    } catch (PDOException $e) {
        // gérer erreur ici si besoin
    }
}

// Le reste du code...


// Fonction badge statut
function getStatusBadge($status) {
    switch ($status) {
        case 'Viewed':
            return '<span class="badge bg-secondary">Vu</span>';
        case 'In Progress':
            return '<span class="badge bg-warning text-dark">En cours</span>';
        case 'Completed':
            return '<span class="badge bg-success">Accomplie</span>';
        default:
            return '<span class="badge bg-dark">Inconnu</span>';
    }
}

checkAdminOrUserLogin();

$message = '';
$message_type = 'danger';  // par défaut rouge (erreur)
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'deleted') {
        $message = "Réponse supprimée avec succès.";
        $message_type = "success";
    } elseif ($_GET['msg'] === 'reply_added') {
        $message = "Réponse ajoutée avec succès.";
        $message_type = "success";
    } elseif ($_GET['msg'] === 'comment_added') {
        $message = "Commentaire ajouté avec succès.";
        $message_type = "success";
    }
}

// Fonction pour nettoyer le contenu Summernote des balises <p> et <br>
function cleanSummernoteContent($html) {
    $html = preg_replace('#^<p>(.*)</p>$#is', '$1', $html);
    $html = str_replace(['<br>', '<br/>', '<br />'], "\n", $html);
    return trim($html);
}
// Supprimer une réponse
if (isset($_POST['delete_reply_id']) && is_numeric($_POST['delete_reply_id'])) {
    $reply_id = (int) $_POST['delete_reply_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM task_comment_replies WHERE reply_id = ?");
        $stmt->execute([$reply_id]);

        $message = "Réponse supprimée avec succès.";
        $message_type = "success";
    } catch (PDOException $e) {
        $message = "Erreur : " . $e->getMessage();
        $message_type = "danger";
    }
}

// Traitement POST : ajout d'un commentaire, d'une réponse, ou mise à jour tâche
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ajouter un commentaire classique
    if (isset($_POST['task_id'], $_POST['task_completion_description'], $_POST['task_status'])) {
        $task_id = (int) $_POST['task_id'];
        $task_completion_description = trim($_POST['task_completion_description']);
        $task_status = $_POST['task_status'];

        // Gestion user_id selon session (admin ou user)
        if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
            $user_id = $_SESSION['user_id'];
        } elseif (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            $user_id = $_SESSION['user_id'];  // <-- Correction ici
        } else {
            $user_id = null;
        }

        if ($task_completion_description === '') {
            $message = "Le commentaire ne peut pas être vide.";
        } elseif ($user_id === null) {
            $message = "Vous devez être connecté pour commenter.";
        } else {
            try {
                // Nettoyer le contenu Summernote
                $task_completion_description = cleanSummernoteContent($task_completion_description);

                // Insérer commentaire dans task_comments
                $stmt = $pdo->prepare("INSERT INTO task_comments (task_id, user_id, comment_text, comment_date) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$task_id, $user_id, $task_completion_description]);

                // Après insertion commentaire
$stmtUser = $pdo->prepare("SELECT task_user_to FROM task_manage WHERE task_id = ?");
$stmtUser->execute([$task_id]);
$recipient_id = $stmtUser->fetchColumn();

if ($recipient_id && $recipient_id != $user_id) {
    $notifContent = "Un nouveau commentaire a été ajouté à la tâche #$task_id.";
    $stmtNotif = $pdo->prepare("INSERT INTO notification_message (user_id, task_id, message) VALUES (?, ?, ?)");
    $stmtNotif->execute([$recipient_id, $task_id, $notifContent]);
}


                // Mettre à jour description globale de la tâche
                $stmt = $pdo->prepare("SELECT task_completion_description FROM task_manage WHERE task_id = ?");
                $stmt->execute([$task_id]);
                $current_description = $stmt->fetchColumn();

                $new_description = $current_description ? $current_description . "\n\n" . $task_completion_description : $task_completion_description;

                $updateStmt = $pdo->prepare("UPDATE task_manage SET task_completion_description = ?, task_status = ?, task_updated_on = NOW() WHERE task_id = ?");
                $updateStmt->execute([$new_description, $task_status, $task_id]);

                header("Location: view_task.php?id=" . $task_id . "&msg=comment_added");
               exit;

                exit;
            } catch (PDOException $e) {
                $message = "Erreur lors de l'enregistrement du commentaire : " . $e->getMessage();
            }
        }
    }


    // Ajouter une réponse à un commentaire
    if (isset($_POST['reply_to_comment_id'], $_POST['reply_text'])) {
    $reply_to_comment_id = (int) $_POST['reply_to_comment_id'];
    $reply_text = cleanSummernoteContent($_POST['reply_text']);
    $task_id = isset($_POST['task_id']) ? (int) $_POST['task_id'] : 0;

    // Gestion du user_id selon session (admin ou utilisateur)
    if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
        $user_id = $_SESSION['user_id'];
    } elseif (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        $user_id = 65;  // ← ID du faux admin présent dans task_user
    } else {
        $user_id = null;
    }

    if ($reply_text === '') {
       $message = "La réponse ne peut pas être vide.";
        $message_type = 'danger';
    } elseif ($user_id === null) {
        $message = "Vous devez être connecté pour répondre.";
    } else {
       try {
    $stmt = $pdo->prepare("INSERT INTO task_comment_replies (comment_id, user_id, reply_text, reply_datetime) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$reply_to_comment_id, $user_id, $reply_text]);

    // Redirection pour éviter la duplication à l'actualisation
    header("Location: view_task.php?id=" . $task_id . "&msg=reply_added");
    exit;
} catch (PDOException $e) {
    $message = "Erreur lors de l'enregistrement de la réponse : " . $e->getMessage();
    $message_type = 'danger';
}
    }
}

}

// Récupération ID tâche via GET
$task_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($task_id <= 0) {
    echo "<div class='alert alert-danger'>ID de la tâche invalide !</div>";
    exit;
}

// Récupérer la tâche avec infos utilisateurs et département
$sql = "
    SELECT tm.*, td.department_name, tu.user_first_name, tu.user_last_name, tu.user_image
    FROM task_manage tm
    JOIN task_department td ON tm.task_department_id = td.department_id
    JOIN task_user tu ON tm.task_user_to = tu.user_id
    WHERE tm.task_id = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$task_id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    echo "<div class='alert alert-danger'>Tâche non trouvée ou non disponible !</div>";
    exit;
}

// Mise à jour auto du statut 'Pending' en 'Viewed' si besoin (quand connecté utilisateur assigné)
if (
    $task['task_status'] === 'Pending' &&
    isset($_SESSION['user_logged_in']) &&
    $_SESSION['user_id'] == $task['task_user_to']
) {
    $updateStatus = $pdo->prepare("UPDATE task_manage SET task_status = ?, task_updated_on = NOW() WHERE task_id = ?");
    $updateStatus->execute(['Viewed', $task_id]);
    $task['task_status'] = 'Viewed';
}

// Récupérer tous les commentaires liés à cette tâche, avec infos utilisateur
$commentsStmt = $pdo->prepare("
    SELECT c.comment_id, c.comment_text, c.comment_date, u.user_first_name, u.user_last_name, u.user_image
    FROM task_comments c
    JOIN task_user u ON c.user_id = u.user_id
    WHERE c.task_id = ?
    ORDER BY c.comment_date ASC
");
$commentsStmt->execute([$task_id]);
$comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer toutes les réponses aux commentaires (groupées par comment_id)
$repliesStmt = $pdo->prepare("
    SELECT r.reply_id, r.comment_id, r.reply_text, r.reply_date, u.user_first_name, u.user_last_name, u.user_image
    FROM task_comment_replies r
    JOIN task_user u ON r.user_id = u.user_id
    WHERE r.comment_id IN (SELECT comment_id FROM task_comments WHERE task_id = ?)
    ORDER BY r.reply_date ASC
");
$repliesStmt->execute([$task_id]);
$allReplies = $repliesStmt->fetchAll(PDO::FETCH_ASSOC);

// Regrouper les réponses par comment_id pour un accès rapide
$repliesByComment = [];
foreach ($allReplies as $reply) {
    $repliesByComment[$reply['comment_id']][] = $reply;
}

include('header.php');
?>

<style>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}
.fade-in {
  animation: fadeIn 1s ease forwards;
}
.task-container {
  display: flex;
  gap: 1.5rem;
  flex-wrap: wrap;
  margin-bottom: 2rem;
}
.task-block {
  flex: 1 1 45%;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 12px rgb(0 0 0 / 0.1);
  padding: 1.5rem;
  animation: fadeIn 1s ease forwards;
}
@media (max-width: 767px) {
  .task-block {
    flex-basis: 100%;
  }
}
.task-block h3 {
  margin-bottom: 1rem;
  border-bottom: 2px solid #007bff;
  padding-bottom: 0.3rem;
  color: #007bff;
}
.comment {
  border-bottom: 1px solid #ddd;
  padding: 0.75rem 0;
  position: relative;
}
.comment:last-child {
  border-bottom: none;
}
.comment .author {
  font-weight: bold;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.comment .author img {
  border-radius: 50%;
}
.comment .date {
  color: #666;
  font-size: 0.9em;
}
.comment .content {
  white-space: pre-wrap;
  margin-top: 0.5rem;
}
.comment .reply-button {
  margin-top: 0.5rem;
}
.reply-form {
  margin-top: 0.5rem;
  margin-left: 2rem;
}
.reply {
  margin-left: 2rem;
  border-left: 3px solid #007bff;
  padding-left: 1rem;
  margin-top: 0.5rem;
}
.reply .author {
  font-weight: bold;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.reply .author img {
  border-radius: 50%;
}
.reply .date {
  color: #666;
  font-size: 0.85em;
}
.reply .content {
  white-space: pre-wrap;
  margin-top: 0.3rem;
}
</style>

<h1 class="mt-4 fade-in">Détails de la tâche</h1>
<ol class="breadcrumb mb-4 fade-in">
    <?php if (isset($_SESSION['admin_logged_in'])): ?>
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
    <?php endif; ?>
    <li class="breadcrumb-item"><a href="task.php">Gestion des tâches</a></li>
    <li class="breadcrumb-item active">Détails de la tâche</li>
</ol>

<?php if ($message): ?>
    <div class="alert alert-<?= htmlspecialchars($message_type) ?> fade-in"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="task-container">

  <!-- Bloc 1 : Infos générales -->
  <div class="task-block fade-in" style="animation-delay: 0.2s;">
    <h3>Informations générales</h3>
    <p><strong>Titre :</strong> <?= htmlspecialchars($task['task_title']) ?></p>
    <p><strong>Département :</strong> <?= htmlspecialchars($task['department_name']) ?></p>
    <p><strong>Stagiaire :</strong> <?= htmlspecialchars($task['user_first_name'] . ' ' . $task['user_last_name']) ?></p>
    <p><strong>Date de début :</strong> <?= htmlspecialchars($task['task_assign_date']) ?></p>
    <p><strong>Date de fin :</strong> <?= htmlspecialchars($task['task_end_date']) ?></p>
    <p><strong>Statut :</strong> <?= getStatusBadge($task['task_status']) ?></p>
    <p><strong>Description :</strong></p>
    <div>
      <?php
      $allowedTags = '<p><br><b><strong><i><em><ul><li><ol>';
      echo strip_tags($task['task_creator_description'], $allowedTags);
      ?>
    </div>
  </div>

  <!-- Bloc 2 : Commentaires -->
  <div class="task-block fade-in" style="animation-delay: 0.4s;">
    <h3>Commentaires</h3>

    <?php if (count($comments) === 0): ?>
      <em>Aucun commentaire pour le moment.</em>
    <?php else: ?>
      <?php foreach ($comments as $comment): ?>
        <div class="comment" id="comment-<?= $comment['comment_id'] ?>">
          <div class="author">
            <img src="<?= htmlspecialchars($comment['user_image']) ?>" width="30" alt="Profil"/>
            <?= htmlspecialchars($comment['user_first_name'] . ' ' . $comment['user_last_name']) ?>
          </div>
          <div class="date"><?= htmlspecialchars($comment['comment_date']) ?></div>
          <div class="content"><?= nl2br(htmlspecialchars($comment['comment_text'])) ?></div>

          <?php if (isset($_SESSION['admin_logged_in'])): ?>
            <button class="btn btn-sm btn-link reply-button" data-comment-id="<?= $comment['comment_id'] ?>">Répondre</button>

            <form method="POST" action="view_task.php?id=<?= $task_id ?>" class="reply-form" id="reply-form-<?= $comment['comment_id'] ?>" style="display:none;">
              <input type="hidden" name="task_id" value="<?= $task_id ?>">
              <input type="hidden" name="reply_to_comment_id" value="<?= $comment['comment_id'] ?>">
              <div class="mb-2">
                <textarea name="reply_text" class="form-control summernote-reply" rows="3" placeholder="Votre réponse..."></textarea>
              </div>
              <button type="submit" class="btn btn-primary btn-sm">Envoyer</button>
              <button type="button" class="btn btn-secondary btn-sm cancel-reply" data-comment-id="<?= $comment['comment_id'] ?>">Annuler</button>
            </form>
          <?php endif; ?>

          <!-- Affichage des réponses -->
          <?php if (isset($repliesByComment[$comment['comment_id']])): ?>
            <?php foreach ($repliesByComment[$comment['comment_id']] as $reply): ?>
              <div class="reply">
                <div class="author">
                  <img src="<?= htmlspecialchars($reply['user_image']) ?>" width="25" alt="Profil"/>
                  <?= htmlspecialchars($reply['user_first_name'] . ' ' . $reply['user_last_name']) ?>
                </div>
                <?php if (isset($_SESSION['admin_logged_in'])): ?>
<a href="view_task.php?id=<?= $task_id ?>&delete_reply_id=<?= $reply['reply_id'] ?>" 
   class="btn btn-danger btn-sm" style="margin-top: 5px;">Supprimer</a>


<?php endif; ?>

                <div class="date"><?= htmlspecialchars($reply['reply_date']) ?></div>
                <div class="content"><?= nl2br(strip_tags($reply['reply_text'], '<br>')) ?></div>

              </div>
            <?php endforeach; ?>
          <?php endif; ?>

        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <?php if (
      isset($_SESSION['user_logged_in']) &&
      $_SESSION['user_id'] == $task['task_user_to'] &&
      in_array($task['task_status'], ['Viewed', 'In Progress'])
    ): ?>
      <?php if (isset($_GET['action']) && $_GET['action'] === 'add_comment'): ?>
        <form method="POST" action="view_task.php?id=<?= $task_id ?>">
          <div class="mb-3 mt-3">
            <label for="task_completion_description"><b>Ajouter un commentaire</b></label>
            <textarea name="task_completion_description" id="task_completion_description" class="form-control summernote" rows="6" required></textarea>
          </div>
          <div class="mb-3">
            <label for="task_status">Statut de la tâche</label>
            <select name="task_status" class="form-select" id="task_status" required>
              <option value="Viewed" <?= $task['task_status'] === 'Viewed' ? 'selected' : '' ?>>Vue</option>
              <option value="In Progress" <?= $task['task_status'] === 'In Progress' ? 'selected' : '' ?>>En cours</option>
              <option value="Completed" <?= $task['task_status'] === 'Completed' ? 'selected' : '' ?>>Accomplie</option>
            </select>
          </div>
          <input type="hidden" name="task_id" value="<?= $task_id ?>">
          <div class="text-center">
            <button type="submit" class="btn btn-success">Valider</button>
          </div>
        </form>
      <?php else: ?>
        <a href="view_task.php?id=<?= $task_id ?>&action=add_comment" class="btn btn-primary mt-3">Ajouter un commentaire</a>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>

<?php include('footer.php'); ?>

<!-- Summernote -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
<script>
$(document).ready(function () {
    $('.summernote').summernote({
        height: 200
    });

    $('.summernote-reply').summernote({
        height: 100
    });

    // Afficher/cacher formulaire réponse
    $('.reply-button').click(function() {
        var commentId = $(this).data('comment-id');
        $('#reply-form-' + commentId).slideDown();
        $(this).hide();
    });

    $('.cancel-reply').click(function() {
        var commentId = $(this).data('comment-id');
        $('#reply-form-' + commentId).slideUp();
        $('.reply-button[data-comment-id="' + commentId + '"]').show();
    });
});
</script>
