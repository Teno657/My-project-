
<?php
require_once 'db_connect.php';
require_once 'auth_function.php';

checkAdminOrUserLogin();

$uid = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] ? $_SESSION['user_id'] : null;

// Mise à jour des tâches en retard
$now = date('Y-m-d');

$updateDelayedSql = "
  UPDATE task_manage 
  SET task_status = 'Delayed' 
  WHERE task_status NOT IN ('Completed', 'Delayed') 
    AND task_end_date < ?
";

$stmt = $pdo->prepare($updateDelayedSql);
$stmt->execute([$now]);


$sqls = [
  'pending' => [
    "sql" => "SELECT COUNT(*) FROM task_manage WHERE task_status IN ('Pending','Viewed')" . ($uid ? " AND task_user_to=?" : ""),
    "param" => $uid
  ],
  'process' => [
    "sql" => "SELECT COUNT(*) FROM task_manage WHERE task_status='In Progress'" . ($uid ? " AND task_user_to=?" : ""),
    "param" => $uid
  ],
  'completed' => [
    "sql" => "SELECT COUNT(*) FROM task_manage WHERE task_status='Completed'" . ($uid ? " AND task_user_to=?" : ""),
    "param" => $uid
  ],
  'delayed' => [
    "sql" => "SELECT COUNT(*) FROM task_manage WHERE task_status='Delayed'" . ($uid ? " AND task_user_to=?" : ""),
    "param" => $uid
  ],
];

foreach ($sqls as $k => $data) {
  $stmt = $pdo->prepare($data['sql']); // ✅ maintenant une chaîne
  if ($uid) {
    $stmt->execute([$data['param']]); // avec paramètre
  } else {
    $stmt->execute(); // sans paramètre
  }
  $$k = $stmt->fetchColumn(); // stocke le résultat dans $pending, $process, etc.
}


include('header.php');
?>

<style>
body {
  font-family: Arial, sans-serif;
  background: url('https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=1470&q=80') no-repeat center center fixed;
  background-size: cover;
  color: #1e293b;
  padding: 1rem 2rem;
  margin: 0;
  min-height: 100vh;
  position: relative;
  z-index: 0;
}
body::before {
  content: "";
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: -1;
}

h1 {
  text-align: center;
  font-size: 2.7rem;
  font-weight: 900;
  margin-bottom: 2.5rem;
  color: #f1f5f9;
  text-shadow: 0 0 12px rgba(0,0,0,0.8), 0 0 20px rgba(255,255,255,0.3);
  letter-spacing: 1px;
  animation: fadeInDown 1s ease;
}

@keyframes fadeInDown {
  from { opacity: 0; transform: translateY(-30px); }
  to { opacity: 1; transform: translateY(0); }
}

.cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.card {
  background: rgba(255,255,255,0.85);
  border-radius: 12px;
  padding: 1rem;
  text-align: center;
  box-shadow: 0 6px 20px rgba(0,0,0,0.2);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  backdrop-filter: blur(8px);
  animation: fadeInUp 0.6s ease both;
}
.card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 30px rgba(0,0,0,0.4);
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.circle {
  width: 70px; height: 70px;
  border-radius: 50%;
  margin: 0 auto 0.8rem;
  display: flex; justify-content: center; align-items: center;
  font-weight: 700; font-size: 28px; color: #fff;
  position: relative;
  overflow: visible;
  text-shadow: 0 0 6px rgba(255,255,255,0.8);
}

/* Dégradé animé */
@keyframes gradientShift {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

.bg-pending {
  background: linear-gradient(270deg, #3b82f6, #60a5fa, #3b82f6);
  background-size: 600% 600%;
  animation: gradientShift 6s ease infinite;
  box-shadow: 0 0 20px #3b82f6;
}
.bg-process {
  background: linear-gradient(270deg, #f97316, #fb923c, #f97316);
  background-size: 600% 600%;
  animation: gradientShift 5s ease infinite;
  box-shadow: 0 0 20px #f97316;
}
.bg-completed {
  background: linear-gradient(270deg, #22c55e, #4ade80, #22c55e);
  background-size: 600% 600%;
  animation: gradientShift 7s ease infinite;
  box-shadow: 0 0 20px #22c55e;
}
.bg-delayed {
  background: linear-gradient(270deg, #ef4444, #f87171, #ef4444);
  background-size: 600% 600%;
  animation: gradientShift 4s ease infinite;
  box-shadow: 0 0 20px #ef4444;
}

.circle::before {
  content: "";
  position: absolute;
  top: -15px; left: -15px;
  width: 100px; height: 100px;
  border: 4px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  animation: rotateCircle 5s linear infinite;
  box-sizing: border-box;
  opacity: 0.6;
}

@keyframes rotateCircle {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

h5 {
  margin: 0;
  font-weight: 600;
  color: #1e293b;
  user-select: none;
}

.table-container {
  max-width: 100vw;
  overflow-x: auto;
  box-shadow: 0 4px 15px rgba(0,0,0,0.25);
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(8px);
}

table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
}
thead {
  background: #2563eb;
  color: #fff;
  font-weight: 600;
}
thead th {
  padding: 0.7rem 1rem;
  text-align: center;
}
tbody td {
  padding: 0.6rem 1rem;
  text-align: center;
}
tbody tr:hover {
  background: #e0e7ff;
  cursor: pointer;
}

.badge {
  padding: 0.25em 0.6em;
  border-radius: 10px;
  color: #fff;
  font-weight: 600;
  font-size: 0.85rem;
  display: inline-block;
}
.badge-pending { background: #3b82f6; }
.badge-viewed { background: #0ea5e9; }
.badge-process { background: #f97316; }
.badge-completed { background: #22c55e; }
.badge-delayed { background: #ef4444; }

.btn {
  font-weight: 600;
  border: none;
  padding: 0.3em 0.6em;
  border-radius: 6px;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  transition: background-color 0.25s ease;
}
.btn-primary { background: #2563eb; color: #fff; }
.btn-primary:hover { background: #1e40af; }
.btn-warning { background: #f59e0b; color: #fff; }
.btn-warning:hover { background: #b45309; }
.btn-danger { background: #dc2626; color: #fff; }
.btn-danger:hover { background: #991b1b; }
</style>

<h1>📊 Tableau de bord</h1>

<div class="cards">
  <div class="card">
    <div class="circle bg-pending"><?php echo $pending ?></div>
    <h5>Tâches en attente</h5>
  </div>
  <div class="card">
    <div class="circle bg-process"><?php echo $process ?></div>
    <h5>Tâches en cours</h5>
  </div>
  <div class="card">
    <div class="circle bg-completed"><?php echo $completed ?></div>
    <h5>Tâches accomplies</h5>
  </div>
  <div class="card">
    <div class="circle bg-delayed"><?php echo $delayed ?></div>
    <h5>Tâches en retard</h5>
  </div>
</div>

<div class="table-container">
  <table id="taskTable">
    <thead>
      <tr>
        <th>ID</th><th>Département</th><th>Stagiaire</th><th>Titre</th><th>Début</th><th>Fin</th><th>Statut</th><th>Actions</th>
      </tr>
    </thead>
  </table>
</div>

<?php include('footer.php'); ?>

<script>
$(function(){
  $('#taskTable').DataTable({
    processing:true,
    serverSide:true,
    ajax: { url:"task_ajax.php", type:"GET" },
    columns:[
      {data:"task_id"},
      {data:"department_name"},
      {
        data:null,
        render:(d,t,r)=>`<img src="${r.user_image}" style="width:30px; border-radius:50%; margin-right:6px;">${r.user_first_name} ${r.user_last_name}`
      },
      {data:"task_title"},
      {data:"task_assign_date"},
      {data:"task_end_date"},
      {
        data:null,
        render:(d,t,r)=>{
          const cls = {
            'Pending':'badge-pending',
            'Viewed':'badge-viewed',
            'In Progress':'badge-process',
            'Completed':'badge-completed',
            'Delayed':'badge-delayed'
          }[r.task_status] || '';
          const txt = {
            'Pending':'En attente',
            'Viewed':'Vue',
            'In Progress':'En cours',
            'Completed':'Accomplie',
            'Delayed':'En retard'
          }[r.task_status] || r.task_status;
          return `<span class="badge ${cls}">${txt}</span>`;
        }
      },
      {
        data:null,
        orderable:false,
        render:(d,t,r)=>{
          let btns = `<a href="view_task.php?id=${r.task_id}" class="btn btn-primary btn-sm me-1">Voir</a>`;
          <?php if(isset($_SESSION["admin_logged_in"])) { ?>
          if(r.task_status=='Pending'||r.task_status=='Viewed'){
            btns += `<a href="edit_task.php?id=${r.task_id}" class="btn btn-warning btn-sm me-1">Modifier</a>`;
          }
          <?php } ?>
          return `<div style="display:flex; justify-content:center;">${btns}</div>`;
        }
      }
    ],
    language: {
      lengthMenu: "Afficher _MENU_ entrées",
      search: "Recherche :",
      info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
      infoEmpty: "Affichage de 0 à 0 sur 0 entrées",
      infoFiltered: "(filtré à partir de _MAX_ entrées au total)",
      paginate: {
        first: "Premier",
        last: "Dernier",
        next: "Suivant",
        previous: "Précédent"
      },
      zeroRecords: "Aucune tache trouvée",
      processing: "Traitement en cours..."
    },
    lengthMenu:[5,10,25,50],
    pageLength:10,
    responsive:true
  });

  $(document).on('click','.btn-delete',function(){
    if(confirm("Voulez-vous réellement supprimer cette tâche ?")){
      window.location.href = 'task.php?id='+$(this).data('id')+'&action=delete';
    }
  });
});
</script>
