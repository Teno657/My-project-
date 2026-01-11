<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>A Propos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      margin: 0;
      padding: 0;
      background: url('https://images.unsplash.com/photo-1504384308090-c894fdcc538d') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', sans-serif;
    }
    .overlay {
      background-color: rgba(0, 0, 0, 0.6);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      color: white;
      text-align: center;
    }
    h1 {
      font-size: 3rem;
      margin-bottom: 20px;
    }
    .btn-custom {
      padding: 12px 30px;
      font-size: 1.1rem;
      border-radius: 30px;
      margin: 10px;
      transition: 0.3s ease;
    }
    .btn-custom:hover {
      transform: scale(1.05);
    }
  </style>
</head>
<body>
  <div class="overlay">
    <h1>Bienvenue sur <strong>StageManager</strong></h1>
    <p>La plateforme intelligente pour la gestion et le suivi des stagiaires</p>
<div class="mentions-legales">
  <h4>À propos de nous</h4>
  <p><strong>StageManager</strong> est une plateforme conçue pour faciliter la gestion, le suivi et l’évaluation des stagiaires au sein d’un établissement ou d’une entreprise.</p>

  <p>Notre objectif est de fournir une solution simple, moderne et efficace permettant aux responsables de stage (admins) d’attribuer des tâches, de suivre leur progression en temps réel et de recevoir des retours instantanés.</p>

  <p>Les stagiaires disposent d’un espace personnalisé où ils peuvent consulter leurs tâches, les accomplir, recevoir des notifications et rester organisés tout au long de leur période de stage.</p>

  <p><strong>Ce projet a été réalisé dans le cadre d’un stage académique par Herman Joris</strong>, avec pour but de répondre à un besoin concret en matière de digitalisation de la gestion des stagiaires.</p>

  <p>StageManager a été développé avec des technologies web modernes (PHP, MySQL, AJAX, Bootstrap) et une interface pensée pour être intuitive et professionnelle.</p>
</div>

<a href="index.php" class="btn btn-light btn-custom mt-4">← Retour à l’accueil</a>
</body>
</html>
