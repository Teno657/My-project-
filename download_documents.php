<?php
require_once 'auth_function.php';
checkAdminLogin();

require_once 'db_connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit('ID invalide.');
}

$user_id = (int) $_GET['id'];

// Récupérer les chemins des documents
$stmt = $pdo->prepare("SELECT user_first_name, user_last_name, cv_path, motivation_letter_path, identity_doc_path FROM task_user WHERE user_id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    exit('Utilisateur non trouvé.');
}

$zip = new ZipArchive();
$zip_filename = tempnam(sys_get_temp_dir(), 'docs_') . '.zip';

if ($zip->open($zip_filename, ZipArchive::CREATE) !== TRUE) {
    exit("Impossible de créer l'archive ZIP.");
}

// Ajouter chaque fichier s'il existe
$docs = [
    'CV.pdf' => $user['cv_path'],
    'Lettre_de_motivation.pdf' => $user['motivation_letter_path'],
    'Piece_identite' . pathinfo($user['identity_doc_path'], PATHINFO_EXTENSION) => $user['identity_doc_path'],
];

foreach ($docs as $name => $file_path) {
    if (file_exists($file_path)) {
        $zip->addFile($file_path, $name);
    }
}

$zip->close();

// Forcer le téléchargement
header('Content-Type: application/zip');
header('Content-disposition: attachment; filename="documents_' . $user['user_first_name'] . '_' . $user['user_last_name'] . '.zip"');
header('Content-Length: ' . filesize($zip_filename));
readfile($zip_filename);
unlink($zip_filename); // Nettoyage du fichier temporaire
exit;
?>
