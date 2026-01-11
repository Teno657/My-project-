<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mentions</title>
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
    .mentions-legales {
      margin-top: 40px;
      max-width: 700px;
      background-color: rgba(255, 255, 255, 0.1);
      padding: 20px;
      border-radius: 15px;
    }
    .mentions-legales h4 {
      font-size: 1.5rem;
      margin-bottom: 15px;
      color: #ffd;
    }
    .mentions-legales p {
      font-size: 1rem;
      line-height: 1.6;
    }
  </style>
</head>
<body>
  <div class="overlay">
    <h1>Bienvenue sur <strong>StageManager</strong></h1>
    <p>La plateforme intelligente pour la gestion et le suivi des stagiaires</p>

    <div class="mentions-legales">
      <h4>Mentions légales</h4>
      <p><strong>Nom du site :</strong> Stage Manager</p>
      <p><strong>Responsable de publication :</strong> Herman Teno</p>
      <p><strong>Email :</strong>stagemanager@icloud.com</p>
      <p><strong>Hébergeur :</strong> Localhost / XAMPP (en développement local)</p>
      <p><strong>Développement :</strong> Projet réalisé dans le cadre d’un stage académique.</p>
      <p>Les données personnelles collectées sont utilisées uniquement dans le cadre de la gestion des stagiaires et ne sont ni revendues ni partagées.</p>
      <p>Conformément à la loi, vous disposez d’un droit d’accès, de rectification et de suppression de vos données personnelles.</p>
    </div>

    <a href="index.php" class="btn btn-light btn-custom mt-4">← Retour à l’accueil</a>
  </div>
</body>
</html>
