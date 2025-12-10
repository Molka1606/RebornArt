<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: signIn.html");
    exit;
}

$user = $_SESSION['user']; 

// ✅ CONNEXION BASE
require_once __DIR__ . "/../model/config.php";
$pdo = config::getConnexion();

/* =====================================================
   ✅ 1) STATISTIQUES UTILISATEURS PAR MOIS (SANS ADMINS)
===================================================== */

$sql = "
SELECT MONTH(created_at) AS mois, COUNT(*) AS total
FROM User
WHERE role = 'user'
GROUP BY MONTH(created_at)
ORDER BY mois
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$statUsers = array_fill(0, 12, 0);

foreach ($result as $row) {
    $statUsers[$row['mois'] - 1] = $row['total'];
}

/* =====================================================
   ✅ 2) UTILISATEURS ACTIFS / INACTIFS (SANS ADMINS)
===================================================== */

$sqlStatus = "
SELECT status, COUNT(*) as total 
FROM User 
WHERE role = 'user'
GROUP BY status
";

$stmtStatus = $pdo->prepare($sqlStatus);
$stmtStatus->execute();
$resultStatus = $stmtStatus->fetchAll(PDO::FETCH_ASSOC);

$active = 0;
$inactive = 0;

foreach ($resultStatus as $row) {
    if ($row['status'] === 'active') {
        $active = $row['total'];
    } elseif ($row['status'] === 'inactive') {
        $inactive = $row['total'];
    }
}

/* =====================================================
   ✅ 3) POURCENTAGES AUTOMATIQUES
===================================================== */

$totalUsers = $active + $inactive;

$percentActive = $totalUsers > 0 ? round(($active / $totalUsers) * 100) : 0;
$percentInactive = $totalUsers > 0 ? round(($inactive / $totalUsers) * 100) : 0;

/* =====================================================
   ✅ 4) NOMBRE TOTAL DES UTILISATEURS (SANS ADMINS)
===================================================== */

$sqlTotalUsers = "
SELECT COUNT(*) AS total 
FROM User 
WHERE role = 'user'
";

$stmtTotal = $pdo->prepare($sqlTotalUsers);
$stmtTotal->execute();
$rowTotal = $stmtTotal->fetch(PDO::FETCH_ASSOC);

$totalUsersGlobal = $rowTotal['total'];

/* =====================================================
   ✅ 5) NOUVEAUX UTILISATEURS CE MOIS (SANS ADMINS)
===================================================== */

$sqlNewUsersMonth = "
SELECT COUNT(*) AS total 
FROM User 
WHERE role = 'user'
AND MONTH(created_at) = MONTH(CURRENT_DATE())
AND YEAR(created_at) = YEAR(CURRENT_DATE())
";

$stmtNew = $pdo->prepare($sqlNewUsersMonth);
$stmtNew->execute();
$rowNew = $stmtNew->fetch(PDO::FETCH_ASSOC);

$newUsersThisMonth = $rowNew['total'];

/* =====================================================
   ✅ 6) COMPTES DÉSACTIVÉS (SANS ADMINS)
===================================================== */

$sqlDisabledUsers = "
SELECT COUNT(*) AS total 
FROM User 
WHERE role = 'user'
AND status = 'inactive'
";

$stmtDisabled = $pdo->prepare($sqlDisabledUsers);
$stmtDisabled->execute();
$rowDisabled = $stmtDisabled->fetch(PDO::FETCH_ASSOC);

$disabledUsers = $rowDisabled['total'];

/* =====================================================
   ✅ 7) DERNIER UTILISATEUR INSCRIT (SANS ADMINS)
===================================================== */

$sqlLastUser = "
SELECT nom, prenom, created_at 
FROM User 
WHERE role = 'user'
ORDER BY created_at DESC
LIMIT 1
";

$stmtLast = $pdo->prepare($sqlLastUser);
$stmtLast->execute();
$lastUser = $stmtLast->fetch(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">


    <title>SB Admin 2 - Dashboard</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">


</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="../../../index.php">
            <div class="">
                <img src="assets/images/logo.png" alt="Logo" style="width: 75px; height:75px;">
            </div>
            <div class="sidebar-brand-text mx-3"></div>
        </a>
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Dashboard -->
        <li class="nav-item">
            <a class="nav-link" href="../../index.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Accueil</span>
            </a>
        </li>
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">Interface</div>

        <!-- Nav Item - Utilisateurs -->
        <li class="nav-item active">
            <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseTwo"
               aria-expanded="true" aria-controls="collapseTwo">
                <i class="fas fa-fw fa-cog"></i>
                <span>Utilisateurs</span>
            </a>
            <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Custo:</h6>
                    <a class="collapse-item active" href="admin/tables.php">Liste des utilisateurs</a>
                </div>
            </div>
        </li>
         <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Demande des Créations</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Utilities:</h6>
                        <a class="collapse-item" href="utilities-color.html">Colors</a>
                        <a class="collapse-item" href="utilities-border.html">Borders</a>
                        <a class="collapse-item" href="utilities-animation.html">Animations</a>
                        <a class="collapse-item" href="utilities-other.html">Other</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <!-- <div class="sidebar-heading">
                Addons
            </div> -->

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
                    aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Pages</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Login Screens:</h6>
                        <a class="collapse-item" href="views/admin/adminLogin.php">Login</a>
                        <a class="collapse-item" href="register.html">Register</a>
                        <a class="collapse-item" href="forgot-password.html">Forgot Password</a>
                        <div class="collapse-divider"></div>
                        <h6 class="collapse-header">Other Pages:</h6>
                        <a class="collapse-item" href="404.html">404 Page</a>
                        <a class="collapse-item" href="blank.html">Blank Page</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Charts -->
            <li class="nav-item">
                <a class="nav-link" href="admin/profiladmin.php">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Profil</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a class="nav-link"  href="admin/logout.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Déconnecter</span></a>
            </li>


            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>

        <!-- Autres menus... -->
        <!-- Garde le reste du sidebar tel quel -->
    </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>


                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>




                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <?php echo htmlspecialchars($user['nom'] . ' ' . $user['prenom']); ?>
                            </span>
                                <img class="img-profile rounded-circle"
                                    src="assets/images/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="../admin/profiladmin.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profil
                                </a>

                                <a class="dropdown-item" href="../forgot-password.html">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Changer le mot de passe
                                </a>

                                <a class="dropdown-item" href="../admin/logout.php">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Déconnecter
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Nombre total d’utilisateurs -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total des utilisateurs
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php echo $totalUsersGlobal; ?>
                                            </div>
                                        </div>

                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>


                    <!-- Nouveaux utilisateurs ce mois -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">

                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Nouveaux utilisateurs (ce mois)
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $newUsersThisMonth; ?>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Comptes désactivés -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">

                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                            Comptes désactivés
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $disabledUsers; ?>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <i class="fas fa-user-slash fa-2x text-gray-300"></i>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>


<!-- Dernier utilisateur inscrit -->
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-info shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">

                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                        Dernier utilisateur inscrit
                    </div>

                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                        <?php 
                        if ($lastUser) {
                            echo htmlspecialchars($lastUser['prenom'] . " " . $lastUser['nom']);
                        } else {
                            echo "Aucun utilisateur";
                        }
                        ?>
                    </div>

                    <div style="font-size:12px; color:#777;">
                        <?php 
                        if ($lastUser) {
                            echo "Inscrit le : " . date("d/m/Y", strtotime($lastUser['created_at']));
                        }
                        ?>
                    </div>
                </div>

                <div class="col-auto">
                    <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                </div>

            </div>
        </div>
    </div>
</div>

                    </div>

                    <!-- Content Row -->

                    <div class="row">

                        <!-- Area Chart -->
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Statistiques des Utilisateurs</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Dropdown Header:</div>
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div style="height: 320px;">
                                        <canvas id="userChart"></canvas>
                                    </div>



                                </div>
                            </div>
                        </div>

                            <!-- Pie Chart -->
                            <div class="col-xl-4 col-lg-5">
                                <div class="card shadow mb-4">

                                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                        <h6 class="m-0 font-weight-bold text-primary">Utilisateurs Actifs / Inactifs</h6>
                                    </div>

                                    <div class="card-body">

                                        <div class="chart-pie pt-4 pb-2" style="height:250px;">
                                            <canvas id="userStatusChart"></canvas>
                                        </div>

                                        <div class="mt-4 text-center small">
                                            <span class="mr-2">
                                                Actifs: <?= $active ?> (<?= $percentActive ?>%)
                                            </span>
                                            <span class="mr-2">
                                                Inactifs: <?= $inactive ?> (<?= $percentInactive ?>%)
                                            </span>
                                        </div>

                                    </div>
                                </div>
                            </div>

                    </div>

                    <!-- Content Row -->


                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">Ã—</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="assets/js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="assets/js/demo/chart-area-demo.js"></script>
    <script src="assets/js/demo/chart-pie-demo.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const dataUsers = <?= json_encode($statUsers) ?>;
console.log("STAT USERS:", dataUsers); // DEBUG

const canvas = document.getElementById("userChart");

if (canvas) {
    const ctx = canvas.getContext("2d");

    new Chart(ctx, {
        type: "line",
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", 
                     "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [{
                label: "Statistiques des utilisateurs",
                data: dataUsers,
                borderWidth: 3,
                tension: 0.4,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}
</script>


<script>
const ctxStatus = document.getElementById("userStatusChart").getContext("2d");

new Chart(ctxStatus, {
    type: "doughnut",
    data: {
        labels: ["Actifs", "Inactifs"],
        datasets: [{
            data: [<?= $active ?>, <?= $inactive ?>],
            backgroundColor: ["#1cc88a", "#e74a3b"],
            hoverBackgroundColor: ["#17a673", "#c0392b"],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        cutout: "70%",
        responsive: true,
        plugins: {
            legend: { display: false }
        }
    },
});
</script>


</body>

</html>
