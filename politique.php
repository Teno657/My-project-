<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Politique</title>
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
      padding: 20px;
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
      max-width: 800px;
      background-color: rgba(255, 255, 255, 0.1);
      padding: 25px;
      border-radius: 15px;
      text-align: left;
    }
    .mentions-legales h4 {
      font-size: 1.5rem;
      margin-bottom: 15px;
      color: #ffd;
      text-align: center;
    }
    .mentions-legales p, .mentions-legales ul {
      font-size: 1rem;
      line-height: 1.6;
    }
    .mentions-legales ul {
      padding-left: 20px;
    }
  </style>
</head>
<body>
  <div class="overlay">
    <h1>Bienvenue sur <strong>StageManager</strong></h1>
    <p>La plateforme intelligente pour la gestion et le suivi des stagiaires</p>

    <div class="mentions-legales">
      <h4>Politique de confidentialité</h4>

      <p><strong>1. Données collectées</strong><br>
      Lors de l'utilisation de l'application StageManager, nous collectons les informations suivantes :
      nom, prénom, adresse e-mail, photo de profil, identifiants de connexion, ainsi que les données liées aux tâches assignées ou accomplies.</p>

      <p><strong>2. Finalité de la collecte</strong><br>
      Ces données sont utilisées uniquement dans le cadre de la gestion et du suivi des stagiaires, pour :</p>
      <ul>
        <li>Attribuer des tâches</li>
        <li>Notifier les utilisateurs</li>
        <li>Assurer le bon fonctionnement de l’application</li>
      </ul>

      <p><strong>3. Confidentialité</strong><br>
      Les données ne sont ni partagées, ni vendues à des tiers. Seul l’administrateur y a accès pour la gestion du système.</p>

      <p><strong>4. Sécurité</strong><br>
      Nous mettons en œuvre des mesures techniques raisonnables pour sécuriser les données contre l'accès non autorisé ou la perte.</p>

      <p><strong>5. Conservation des données</strong><br>
      Les données sont conservées pendant toute la durée du stage, puis supprimées à la fin du cycle ou sur demande.</p>

      <p><strong>6. Vos droits</strong><br>
      Conformément à la loi informatique et libertés (ou RGPD si applicable), vous disposez d’un droit d’accès, de rectification ou de suppression de vos données. Pour exercer ces droits, contactez-nous à <strong>stagemanager@icloud.com</strong>.</p>
    </div>

    <a href="index.php" class="btn btn-light btn-custom mt-4">← Retour à l’accueil</a>
  </div>
</body>
</html>
