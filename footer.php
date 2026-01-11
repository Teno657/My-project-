                    </div>
                </main>
            </div>
        </div>
        <script src="asset/vendor/jquery/jquery-3.6.0.min.js"></script>
        <script src="asset/vendor/bootstrap/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="asset/vendor/datatables/jquery.dataTables.min.js"></script>
        <script src="asset/vendor/datatables/dataTables.bootstrap5.min.js"></script>
        <script src="asset/js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <?php if (isset($_SESSION['delete_pending'])): ?>
  <div id="deleteOverlay">
    <div class="delete-box text-center">
      <button class="btn btn-danger btn-lg mb-3 d-flex align-items-center justify-content-center" disabled>
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        Suppression du stagiaire dans <span id="countdown">5</span> seconde(s)...
      </button>
      <br>
      <a href="cancel_delete_user.php?id=<?= $_SESSION['delete_pending']['user_id'] ?>" class="btn btn-secondary">
        ❌ Annuler
      </a>
    </div>
  </div>

  <!-- Son de confirmation -->
  <audio id="deleteSound" src="https://www.soundjay.com/buttons/sounds/button-16.mp3" preload="auto"></audio>

  <script>
    const countdownEl = document.getElementById('countdown');
    let countdown = 5; // secondes

    const interval = setInterval(() => {
      countdown--;
      countdownEl.textContent = countdown;

      if (countdown <= 0) {
        clearInterval(interval);
      }
    }, 1000);

    // Jouer le son après 4.5 secondes
    setTimeout(() => {
      document.getElementById('deleteSound').play();
    }, 4500);

    // Lancer l'animation de disparition
    setTimeout(() => {
      document.getElementById('deleteOverlay').style.opacity = '0';
    }, 4800);

    // Rediriger après animation (après 5 secondes)
    setTimeout(() => {
      window.location.href = "delete_user.php?confirm=1&id=<?= $_SESSION['delete_pending']['user_id'] ?>";
    }, 5000);
  </script>

  <style>
    #deleteOverlay {
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0, 0, 0, 0.7);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
      transition: opacity 0.6s ease;
    }

    .delete-box {
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
      max-width: 400px;
      text-align: center;
      animation: scaleIn 0.4s ease;
    }

    @keyframes scaleIn {
      from { transform: scale(0.9); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }
  </style>
<?php unset($_SESSION['delete_pending']); ?>
<?php endif; ?>


    </body>
</html> 