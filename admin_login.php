<?php

if (!file_exists('db_connect.php')) {
    header('Location: install.php');
    exit;
}

require_once 'db_connect.php';
require_once 'auth_function.php';

redirectIfLoggedIn();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['admin_email']);
    $password = trim($_POST['admin_password']);

    if (empty($email)) {
        $errors[] = "votre email est obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "votre email est incorrecte.";
    }

    if (empty($password)) {
        $errors[] = "votre mot de passe est obligatoire.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM task_admin WHERE admin_email = ?");
            $stmt->execute([$email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['admin_password'])) {
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_logged_in'] = true;
                header('Location: dashboard.php');
                exit;
            } else {
                $errors[] = "votre mot de passe est incorrecte.";
            }
        } catch (PDOException $e) {
            $errors[] = "DB ERROR: " . $e->getMessage();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Stage Manager || Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="asset/vendor/bootstrap/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: url('https://images.unsplash.com/photo-1573497491208-6b1acb260507?auto=format&fit=crop&w=1600&q=80') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        .overlay {
            background-color: rgba(0, 0, 0, 0.6);
            min-height: 100vh;
            padding-top: 60px;
            color: white;
        }

        h1 {
            color: white;
            text-shadow: 1px 1px 3px black;
        }

        .card {
            background-color: #ffffffee;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            animation: slideUp 0.8s ease-out 0.2s both;
        }

        @keyframes slideUp {
            from {
                transform: translateY(40px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .card-header {
            background: linear-gradient(45deg, #0d6efd, #0a58ca);
            color: white;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            font-weight: bold;
            text-align: center;
            padding: 1rem;
        }

        .card-body {
            padding: 2rem;
        }

        .form-label {
            font-weight: 600;
            color: #333;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
        }

        .btn-primary {
            width: 100%;
            border-radius: 8px;
            padding: 0.6rem;
            font-weight: bold;
        }

        a {
            text-decoration: none;
            color: #0d6efd;
        }

        a:hover {
            text-decoration: underline;
        }

        .text-muted-custom {
            color: #6c757d;
            font-size: 0.95rem;
        }
        /* Container du formulaire */
.animated-form {
  max-width: 100%;
}

/* Style des groupes input + label */
.animated-form .form-group {
  position: relative;
}

/* Inputs stylés */
.animated-form .form-input {
  width: 100%;
  border: 2px solid #ced4da;
  border-radius: 10px;
  padding: 1.2rem 1rem 0.4rem 1rem;
  font-size: 1.1rem;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
  background-color: #fff;
  color: #333;
}

/* Label au dessus par défaut */
.animated-form .form-label {
  position: absolute;
  top: 1.25rem;
  left: 1rem;
  font-weight: 600;
  color: #666;
  pointer-events: none;
  transition: all 0.3s ease;
  user-select: none;
  background: white;
  padding: 0 0.25rem;
}

/* Quand input est focus ou non vide, label remonte et rétrécit */
.animated-form .form-input:focus,
.animated-form .form-input:not(:placeholder-shown) {
  border-color: #0d6efd;
  box-shadow: 0 0 8px rgba(13, 110, 253, 0.5);
  outline: none;
}

.animated-form .form-input:focus + .form-label,
.animated-form .form-input:not(:placeholder-shown) + .form-label {
  top: 0.25rem;
  left: 1rem;
  font-size: 0.85rem;
  color: #0d6efd;
  font-weight: 700;
}

/* Bouton submit */
.animated-form .btn-submit {
  font-weight: 700;
  font-size: 1.2rem;
  padding: 0.75rem;
  border-radius: 10px;
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.animated-form .btn-submit:hover,
.animated-form .btn-submit:focus {
  background-color: #0b5ed7;
  box-shadow: 0 4px 15px rgba(13, 110, 253, 0.6);
  outline: none;
  color: white;
}

    </style>
</head>
<body>
    <div class="overlay">
        <main>
            <div class="container">
                <h1 class="mb-4 text-center">Admin Connexion</h1>
                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-5">
                        <?php if (!empty($errors)) { ?>
                            <div class="alert alert-danger">
                                <ul class="list-unstyled mb-0">
                                    <?php foreach ($errors as $error) { ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        <?php } ?>
                        <div class="card">
                            <div class="card-header">Connexion Administrateur</div>
                            <div class="card-body">
                           <form method="post" action="" class="animated-form">
  <div class="form-group mb-4">
    <input type="email" id="admin_email" name="admin_email" class="form-input" required autocomplete="off" />
    <label for="admin_email" class="form-label">Adresse email :</label>
  </div>
  <div class="form-group mb-4">
    <input type="password" id="admin_password" name="admin_password" class="form-input" required autocomplete="off" />
    <label for="admin_password" class="form-label">Mot de passe :</label>
  </div>
  <input type="submit" value="Se connecter" class="btn btn-primary btn-submit" />

  <div class="text-center mt-3 text-muted-custom">
    <a href="user_login.php">Connexion stagiaire</a>
  </div>
</form>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
  <a href="index.php" class="btn btn-outline-light btn-lg">
    <i class="bi bi-house-door-fill me-2"></i> Retour à l’accueil
  </a>
</div>
        </main>
    </div>
</body>
</html>
