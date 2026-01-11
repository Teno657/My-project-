<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Stage Manager - Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- FontAwesome -->
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
        }

        /* Conteneur principal en flex row */
        #layoutSidenav {
            display: flex;
            min-height: 100vh;
            height: 100%;
        }

        /* Sidebar à gauche, dans le flux */
        #layoutSidenav_nav {
            width: 280px;
            background: linear-gradient(180deg, #4b6cb7 0%, #182848 100%);
            color: #e0e6f8;
            padding-top: 20px;
            box-shadow: 3px 0 8px rgba(0,0,0,0.1);
            transition: background-color 0.3s ease;
        }
        #layoutSidenav_nav:hover {
            background: linear-gradient(180deg, #3a53a0 0%, #0f1f3e 100%);
        }
        #layoutSidenav_nav .sb-sidenav-menu .nav-link {
            color: #cfd8f7;
            font-weight: 600;
            padding: 12px 30px;
            transition: background-color 0.3s ease, color 0.3s ease;
            border-radius: 8px;
            margin: 4px 16px;
            display: flex;
            align-items: center;
        }
        #layoutSidenav_nav .sb-sidenav-menu .nav-link:hover,
        #layoutSidenav_nav .sb-sidenav-menu .nav-link.active {
            background-color: #ffc107; /* couleur jaune vif */
            color: #182848;
            box-shadow: 0 0 12px #ffc107aa;
            text-shadow: 0 0 5px #0003;
        }
        #layoutSidenav_nav .sb-nav-link-icon {
            margin-right: 12px;
            color: #a0b8f7;
            font-size: 18px;
        }
        #layoutSidenav_nav .sb-sidenav-footer {
            padding: 15px 20px;
            font-size: 0.9rem;
            color: #b0b7d6;
            border-top: 1px solid #3a3a7e;
            text-align: center;
            margin-top: 30px;
        }

        /* Contenu principal à droite */
        #layoutSidenav_content {
            flex-grow: 1;
            background-color: #fff;
            padding: 30px 40px;
            box-shadow: inset 0 0 10px #00000010;
        }

        /* Navbar */
        .sb-topnav {
            background: #243b55;
            color: white;
            padding: 0 1.5rem;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative; /* important pour le positionnement absolu */
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
        .sb-topnav .navbar-brand {
            font-weight: 700;
            font-size: 1.6rem;
            color: #ffc107;
            letter-spacing: 1px;
            transition: color 0.3s ease;
            text-shadow: 0 0 6px #ffc107aa;
        }
        .sb-topnav .navbar-brand:hover {
            color: #fff;
        }
        .sb-topnav .btn-link {
            color: #ffc107;
            font-size: 1.3rem;
            transition: color 0.3s ease;
            border-radius: 4px;
        }
        .sb-topnav .btn-link:hover {
            color: #fff;
            background-color: #ffc107aa;
        }
        .sb-topnav .navbar-nav .nav-link {
            color: #f1f3f8;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .sb-topnav .navbar-nav .nav-link:hover {
            color: #ffc107;
        }

        /* Profile image in navbar */
        .sb-topnav .nav-link img.rounded-circle {
            border: 2px solid #ffc107;
            box-shadow: 0 0 8px #ffc107aa;
            transition: box-shadow 0.3s ease;
        }
        .sb-topnav .nav-link img.rounded-circle:hover {
            box-shadow: 0 0 14px #ffc107ff;
        }

        /* Dropdown menu */
        .dropdown-menu {
            background: #243b55;
            border: none;
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
        }
        .dropdown-item {
            color: #cfd8f7;
            font-weight: 600;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .dropdown-item:hover {
            background-color: #ffc107;
            color: #182848;
        }

        /* Notification animations */
        .notif-badge {
            animation: pulse-badge 1.2s infinite;
        }
        .bell-animate {
            animation: bell-shake 0.6s ease-in-out;
        }
        @keyframes bell-shake {
            0% { transform: rotate(0); }
            25% { transform: rotate(-10deg); }
            50% { transform: rotate(10deg); }
            75% { transform: rotate(-10deg); }
            100% { transform: rotate(0); }
        }
        @keyframes pulse-badge {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.3); opacity: 0.6; }
        }

        /* CENTRER LA CLOCHE */
        .notif-center {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        /* Responsive */
        @media (max-width: 768px) {
            #layoutSidenav {
                flex-direction: column;
            }
            #layoutSidenav_nav {
                width: 100%;
                padding: 10px 0;
                box-shadow: none;
            }
            #layoutSidenav_content {
                padding: 20px;
            }
            .sb-topnav {
                flex-wrap: wrap;
                height: auto;
                padding: 0.5rem 1rem;
            }
            .sb-topnav .btn-link {
                margin-left: auto;
            }
        }
    </style>
</head>
<body>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db_connect.php';

// Récupérer le nombre de notifications non lues
$notifCount = 0;
if (isset($_SESSION['admin_logged_in'])) {
    // Récupérer le nombre de notifications non lues dans la table notifications
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE is_read = 0");
    $stmt->execute();
    $notifCount = (int) $stmt->fetchColumn();
} elseif (isset($_SESSION['user_logged_in'])) {
    // notifications user
    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$userId]);
    $notifCount = (int) $stmt->fetchColumn();
}


// Image de profil selon type utilisateur
$adminProfileImg = 'asset/images/admin.jpg';
$profileImage = (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'])
    ? $adminProfileImg
    : (isset($_SESSION['user_image']) ? $_SESSION['user_image'] : 'asset/images/default-user.png');
?>

<nav class="sb-topnav d-flex align-items-center justify-content-between px-3">
    <!-- Logo à gauche -->
    <div class="d-flex align-items-center">
        <a class="navbar-brand me-4" href="#">Stage Manager</a>
        <button class="btn btn-link d-lg-none" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Cloche notification centrée -->
    <?php if (isset($_SESSION['user_logged_in']) || isset($_SESSION['admin_logged_in'])): ?>
    <div class="notif-center">
       <?php if (isset($_SESSION['user_logged_in']) || isset($_SESSION['admin_logged_in'])): ?>
    <div class="notif-center">
        <a class="nav-link position-relative" href="<?=
            isset($_SESSION['admin_logged_in']) ? 'all_notifications.php' : 'user_notifications.php'
        ?>" title="Notifications">
            <i class="fas fa-bell fa-lg"></i>
            <?php if ($notifCount > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notif-badge">
                    <?= $notifCount ?>
                </span>
            <?php endif; ?>
        </a>
    </div>
<?php endif; ?>

        </a>
    </div>
    <?php endif; ?>

    <!-- Profil à droite -->
    <ul class="navbar-nav d-flex align-items-center">
        <li class="nav-item d-flex align-items-center ms-3">
            <div class="position-relative">
                <img src="<?= htmlspecialchars($profileImage); ?>" alt="Photo de profil" width="60" height="60"
                    class="rounded-circle border border-warning shadow" style="object-fit: cover;">
                <span class="position-absolute bottom-0 end-0 translate-middle p-1 bg-success border border-light rounded-circle"></span>
            </div>
        </li>
    </ul>
</nav>

<div id="layoutSidenav">
    <nav id="layoutSidenav_nav" class="sb-sidenav accordion" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <?php if(isset($_SESSION['admin_logged_in'])): ?>
                    <a class="nav-link" href="dashboard.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Dashboard
                    </a>
                    <a class="nav-link" href="department.php">
                        <div class="sb-nav-link-icon"><i class="far fa-building"></i></div>
                        Département
                    </a>
                    <a class="nav-link" href="user.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-user-md"></i></div>
                        Stagiaires
                    </a>
                    <a class="nav-link" href="task.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-notes-medical"></i></div>
                        Tâches
                    </a>
                    <a class="nav-link" href="admin_change_password.php">
                        <div class="sb-nav-link-icon"><i class="far fa-id-card"></i></div>
                        Changer de mot de passe
                    </a>
                    <a class="nav-link" href="logout.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                        Se déconnecter
                    </a>
                <?php else: ?>
                    <a class="nav-link" href="task.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-notes-medical"></i></div>
                        Tâches
                    </a>
                    <a class="nav-link" href="user_profile.php">
                        <div class="sb-nav-link-icon"><i class="far fa-id-card"></i></div>
                        Profil
                    </a>
                    <a class="nav-link" href="user_change_password.php">
                        <div class="sb-nav-link-icon"><i class="far fa-id-card"></i></div>
                        Changer de mot de passe
                    </a>
                    <a class="nav-link" href="logout.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                        Se déconnecter
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Tous droits réservés</div>
            &copy;2025 Stage Manager
        </div>
    </nav>

<script>
function updateNotificationBadge() {
    fetch('check_notifications.php')
        .then(response => response.json())
        .then(data => {
            const badges = document.querySelectorAll('.notif-badge');
            badges.forEach(badge => {
                const count = parseInt(data.count);
                if (count > 0) {
                    badge.textContent = count;
                    badge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                    badge.textContent = '';
                }
            });
        })
        .catch(console.error);
}
updateNotificationBadge();
setInterval(updateNotificationBadge, 10000);
</script>

<main id="layoutSidenav_content">
    <div class="container-fluid px-4">
        <!-- Ton contenu ici -->
