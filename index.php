<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>StageManager - Accueil</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />


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
    .social-icon:hover {
    color: #a8d0ff !important;
    transition: color 0.3s ease;
  }
  a.text-decoration-none:hover {
    text-decoration: underline;
  }
  </style>
</head>
<body>
  <div class="overlay">
    <h1>Bienvenue sur <strong>StageManager</strong></h1>
    <p>La plateforme pour la gestion et le suivi des stagiaires</p>
    <div class="mt-4">
      <a href="register.php" class="btn btn-outline-light btn-custom">Inscription</a>
      <a href="admin_login.php" class="btn btn-outline-light btn-custom">Connexion</a>
    </div>
  </div>
 <!-- N'oublie pas d'avoir ce lien dans le <head> -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

<footer class="bg-primary text-white py-3">
  <div class="container">
    <div class="row align-items-center text-center text-md-start">

      <!-- À propos + mentions -->
      <div class="col-md-6 mb-3 mb-md-0">
        <p class="mb-2 fs-5 fw-semibold">
          <strong>StageManager</strong> facilite la gestion et le suivi des stagiaires.
        </p>
        <br>
        <nav class="nav justify-content-center justify-content-md-start gap-3" aria-label="Mentions légales">
  <a href="mentions.php" class="text-white opacity-75 text-decoration-underline text-decoration-hover" style="user-select: none;">Mentions légales</a>
  <a href="politique.php" class="text-white opacity-75 text-decoration-underline text-decoration-hover" style="user-select: none;">Politique de confidentialité</a>
  <a href="propos.php" class="text-white opacity-75 text-decoration-underline text-decoration-hover" style="user-select: none;">À propos de nous</a>
  </nav>
      </br>
      </div>

      <!-- Contact + réseaux sociaux -->
      <div class="col-md-6 d-flex flex-column flex-md-row align-items-center justify-content-center justify-content-md-end gap-4 gap-md-5 fs-5">
        <div class="text-center text-md-end" style="min-width: 260px;">
          <div class="d-flex align-items-center justify-content-center justify-content-md-end gap-2 mb-1">
            <i class="bi bi-telephone-fill"></i>
            <a href="tel:+237612345678" class="text-white text-decoration-none fw-semibold">(+237) 6 50 13 31 45</a>
          </div>
          <div class="d-flex align-items-center justify-content-center justify-content-md-end gap-2 mb-1">
            <i class="bi bi-envelope-fill"></i>
            <a href="mailto:contact@stagemanager.com" class="text-white text-decoration-none fw-semibold">stagemanager@icloud.com</a>
          </div>
          <div class="d-flex align-items-center justify-content-center justify-content-md-end gap-2">
            <i class="bi bi-whatsapp"></i>
            <a href="tel:+237671865978"  class="text-white text-decoration-none fw-semibold">WhatsApp (+237) 6 93 50 76 69</a>
          </div>
        </div>

        <div>
          <a href="https://www.facebook.com/stagemanager" target="_blank" class="text-white fs-4 mx-2 social-icon" aria-label="Facebook">
            <i class="bi bi-facebook"></i>
          </a>
          <a href="https://www.instagram.com/stagemanager" target="_blank" class="text-white fs-4 mx-2 social-icon" aria-label="Instagram">
            <i class="bi bi-instagram"></i>
          </a>
          <a href="https://www.twitter.com/stagemanager" target="_blank" class="text-white fs-4 mx-2 social-icon" aria-label="Twitter">
            <i class="bi bi-twitter"></i>
          </a>
          <a href="https://www.linkedin.com/company/stagemanager" target="_blank" class="text-white fs-4 mx-2 social-icon" aria-label="LinkedIn">
            <i class="bi bi-linkedin"></i>
          </a>
          <a href="https://wa.me/935496768" target="_blank" class="text-white fs-4 mx-2 social-icon" aria-label="WhatsApp">
            <i class="bi bi-whatsapp"></i>
          </a>
        </div>
      </div>

    </div>

    <hr class="border-white opacity-25 my-3" />

    <div class="text-center small opacity-75" style="user-select:none;">
      © 2025 <strong>StageManager</strong>. Tous droits réservés.
    </div>
  </div>
</footer>

<style>
  .social-icon:hover {
    color: #a8d0ff !important;
    transition: color 0.3s ease;
  }
  a.text-decoration-none:hover {
    text-decoration: underline;
  }
</style>

</body>
</html>
