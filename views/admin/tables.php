<?php
session_start(); 

require_once __DIR__ . '/../../model/config.php';  // ✔️ AJOUT ESSENTIEL

$connectedUser = isset($_SESSION['user']) ? $_SESSION['user'] : ['nom' => 'Invité', 'prenom' => ''];

// -------------------- AJOUT DE LA SUPPRESSION --------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $deleteId = intval($_POST['delete_user_id']);
    try {
        $db = config::getConnexion();
        $stmtDelete = $db->prepare("DELETE FROM User WHERE id = :id");
        $stmtDelete->execute(['id' => $deleteId]);

        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        echo "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// -------------------------------------------------------------------

try {
    $db = config::getConnexion();

    // Récupération des valeurs GET
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $sort = isset($_GET['sort']) ? $_GET['sort'] : '';

    // Base query
    $query = "SELECT id, nom, prenom, email FROM User WHERE role = :role";

    // Recherche
    if ($search !== '') {
        $query .= " AND (nom LIKE :search OR prenom LIKE :search OR email LIKE :search)";
    }

    // Tri
    switch ($sort) {
        case 'name_asc':
            $query .= " ORDER BY nom ASC";
            break;
        case 'name_desc':
            $query .= " ORDER BY nom DESC";
            break;
        case 'date_asc':
            $query .= " ORDER BY id ASC";
            break;
        case 'date_desc':
            $query .= " ORDER BY id DESC";
            break;
        default:
            $query .= " ORDER BY id DESC";
            break;
    }

// ================== ✅ PAGINATION ==================
$items_per_page = 3;   // nombre d’utilisateurs par page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Compter le total
$countQuery = "SELECT COUNT(*) FROM User WHERE role = :role";

if ($search !== '') {
    $countQuery .= " AND (nom LIKE :search OR prenom LIKE :search OR email LIKE :search)";
}

    $stmtCount = $db->prepare($countQuery);

    $params = ['role' => 'user'];
    if ($search !== '') {
        $params['search'] = "%$search%";
    }

    $stmtCount->execute($params);
    $total_users = $stmtCount->fetchColumn();
    $total_pages = ceil($total_users / $items_per_page);

    // Ajouter la pagination à la requête principale
    $query .= " LIMIT :offset, :limit";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':role', 'user', PDO::PARAM_STR);

    if ($search !== '') {
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    }

    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', (int)$items_per_page, PDO::PARAM_INT);

    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);


} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
    $users = [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
        <!-- Custom fonts for this template-->
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <!-- Custom styles for this template-->
        <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <script src="../assets/js/controle.js"></script>
</head>
<body id="page-top">

<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="../../../index.php">
            <div class="">
                <img src="../assets/images/logo.png" alt="Logo" style="width: 75px; height:75px;">
            </div>
            <div class="sidebar-brand-text mx-3"></div>
        </a>
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Dashboard -->
        <li class="nav-item">
            <a class="nav-link" href="../../../index.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
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
                    <a class="collapse-item active" href="tables.php">Liste des utilisateurs</a>
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

            <li class="nav-item">
                <a class="nav-link" href="../admin/profiladmin.php">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Profil</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a class="nav-link"  href="../admin/logout.php">
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
<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Search -->
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                aria-label="Search" aria-describedby="basic-addon2">
            <div class="input-group-append">
                <button class="btn btn-primary" type="button">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    <?= htmlspecialchars($connectedUser['nom'] . ' ' . $connectedUser['prenom']) ?>
                </span>

                <img class="img-profile rounded-circle" src="../assets/images/undraw_profile.svg">
            </a>

            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">

                <a class="dropdown-item" href="../admin/profiladmin.php">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profil
                </a>

                <a class="dropdown-item" href="views/forgot-password.html">
                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                    Changer le mot de passe
                </a>

                <a class="dropdown-item" href="logout.php">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Déconnecter
                </a>
            </div>
        </li>

    </ul>

</nav>
<!-- End of Topbar -->
            <!-- Begin Page Content -->
            <div class="container-fluid">

                <div class="table-data">
                    <div class="order">
                        <div class="head">
                            <h3>Liste des utilisateurs</h3>
                        </div><!-- Formulaire de recherche / filtre / tri -->
                        <form method="GET" action="" style="margin-bottom: 20px; display:flex; gap:10px;">
                            <input type="text" id="searchInput" name="search">

                        
                            <select name="sort" class="form-control" style="width:200px;">
                                <option value="">Tri par défaut</option>
                                <option value="name_asc"  <?= (isset($_GET['sort']) && $_GET['sort']=="name_asc") ? 'selected' : '' ?>>
                                    Nom A → Z
                                </option>
                                <option value="name_desc" <?= (isset($_GET['sort']) && $_GET['sort']=="name_desc") ? 'selected' : '' ?>>
                                    Nom Z → A
                                </option>
                                <option value="date_desc" <?= (isset($_GET['sort']) && $_GET['sort']=="date_desc") ? 'selected' : '' ?>>
                                    Récents → Anciens
                                </option>
                                <option value="date_asc"  <?= (isset($_GET['sort']) && $_GET['sort']=="date_asc") ? 'selected' : '' ?>>
                                    Anciens → Récents
                                </option>
                            </select>

                            <button class="btn btn-primary">Appliquer</button>
                        </form>

                        <table id="usersTable">

                            <tr>
                                <th>Nom d'utilisateur</th>
                                <th>Prénom d'utilisateur</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                            <?php foreach($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['nom']) ?></td>
                                <td><?= htmlspecialchars($user['prenom']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="delete_user_id" value="<?= $user['id'] ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">Supprimer</button>

                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                        <!-- PAGINATION -->
                        <nav aria-label="Page navigation" style="margin-top:20px;">
                            <ul class="pagination justify-content-center">

                                <!-- Bouton Précédent -->
                                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link"
                                    href="?page=<?= $page - 1 ?>&search=<?= $search ?>&sort=<?= $sort ?>">
                                        Précédent
                                    </a>
                                </li>

                                <!-- Pages numérotées -->
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                        <a class="page-link"
                                        href="?page=<?= $i ?>&search=<?= $search ?>&sort=<?= $sort ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <!-- Bouton Suivant -->
                                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                    <a class="page-link"
                                    href="?page=<?= $page + 1 ?>&search=<?= $search ?>&sort=<?= $sort ?>">
                                        Suivant
                                    </a>
                                </li>

                            </ul>
                        </nav>

                    </div>
                </div>

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

    <!-- Bootstrap core JavaScript-->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../assets/js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="../vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="../assets/js/demo/chart-area-demo.js"></script>
    <script src="../assets/js/demo/chart-pie-demo.js"></script>
        <!-- JS Bootstrap + SB Admin 2 -->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/sb-admin-2.min.js"></script>
    <script>
document.getElementById("searchInput").addEventListener("keyup", function () {
    let value = this.value;

    // Requête AJAX
    fetch("?search=" + encodeURIComponent(value))
        .then(response => response.text())
        .then(data => {

            // Extraire seulement le tableau depuis la page générée
            let parser = new DOMParser();
            let doc = parser.parseFromString(data, "text/html");

            let newTable = doc.querySelector("#usersTable");

            document.querySelector("#usersTable").innerHTML = newTable.innerHTML;
        });
});
</script>

</body>
</html>
