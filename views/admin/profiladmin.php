<?php
session_start();

require_once __DIR__ . '/../../controller/adminController.php';  // ✔️ chemin corrigé

if (!isset($_SESSION['user'])) {
    header("Location: ../../views/index.php");
    exit();
}

$user = $_SESSION['user'];

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = $_POST['password']; 
    $newpassword = $_POST['newpassword'];

    $userC = new adminController();
    $result = $userC->updateProfile($user['id'], $nom, $prenom, $email, $password, $newpassword);

    if ($result) {
        $_SESSION['user']['nom'] = $nom;
        $_SESSION['user']['prenom'] = $prenom;
        $_SESSION['user']['email'] = $email;

        $success = "Profil mis à jour avec succée !";
        $user = $_SESSION['user']; 
    } else {
        $error = "Mot de passe actuel incorrect ou erreur lors de la mise à jour.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin</title>

    <!-- Custom fonts for this template-->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <script src="../assets/js/controle.js"></script>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
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
            <a class="nav-link" href="../index.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Accueil</span>
            </a>
        </li>
        <hr class="sidebar-divider">
        <li class="nav-item active">
                    <a class="nav-link" href="../../views/view/back/showblog.php">
                        <i class="fas fa-fw fa-newspaper"></i>
                        <span>Articles</span>
                    </a>
                </li>
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
                    <a class="collapse-item active" href="../../views/admin/tables.php">Liste des utilisateurs</a>
                </div>
            </div>
        </li>
        



            <!-- Nav Item - Charts -->
            <li class="nav-item">
                <a class="nav-link" href="profiladmin.php">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Profil</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a class="nav-link"  href="../../views/admin/logout.php">
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
                                src="../assets/images/undraw_profile.svg">
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
                            <a class="dropdown-item" href="../admin/logout.php">
                                <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                Déconnecter
                            </a>
                        </div>
                    </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Contenu -->
                <div class="container-fluid">
                    <div class="conteneur">

                        <h2 class="titre-page">Réglages du compte</h2>
                        
                        <?php if($success) echo "<p style='color:green;'>$success</p>"; ?>
                        <?php if($error) echo "<p style='color:red;'>$error</p>"; ?>

                        <div class="side">
                            <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" width="100px";height="100px"; alt="Photo de profil" class="photo">
                        </div>

                        <form action="" method="POST">


                            <div class="form-group">
                                <label for="nom">Nom :</label>
                                <input type="text" id="nom" name="nom" class="form-control" value="<?php echo htmlspecialchars($user['nom']); ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label for="prenom">Prénom :</label>
                                <input type="text" id="prenom" name="prenom" class="form-control" value="<?php echo htmlspecialchars($user['prenom']); ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label for="email">Adresse Email :</label>
                                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label for="password">Mot de passe actuel :</label>
                                <input type="password" id="password" name="password" class="form-control" >
                            </div>

                            <div class="form-group">
                                <label for="newpassword">Nouveau mot de passe :</label>
                                <input type="password" id="newpassword" name="newpassword" class="form-control" readonly>
                            </div>

                            <button type="button" class="btn btn-primary mt-3" id="btnEdit">Mise à jour</button>
                            <button type="submit" class="btn btn-success mt-3" id="btnSubmit" style="display:none;">Enregistrer</button>
                            <button type="reset" class="btn btn-secondary mt-3">Annuler</button>

                        </form>
                        <form action="../admin/delete.php" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer votre compte ?');">
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                            <button type="submit" class="btn btn-danger mt-3">Supprimer mon compte</button>
                        </form>
                        <form method="POST" action="desactiver_admin.php"
                            onsubmit="return confirm('Voulez-vous vraiment désactiver votre compte ?');">
                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                            <button type="submit" class="btn btn-warning mt-3">Désactiver mon compte </button>
                        </form>




                    </div>
                </div>

            </div>
            <!-- End Main Content -->

        </div>
        <!-- End Content Wrapper -->

    </div>
    <!-- End Page Wrapper -->


    <script>
        document.getElementById('btnEdit').addEventListener('click', function() {
            document.getElementById('nom').removeAttribute('readonly');
            document.getElementById('prenom').removeAttribute('readonly');
            document.getElementById('email').removeAttribute('readonly');
            document.getElementById('newpassword').removeAttribute('readonly');

            this.style.display = 'none'; 
            document.getElementById('btnSubmit').style.display = 'inline-block'; 
        });
    </script>

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
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/sb-admin-2.min.js"></script>
    

</body>

</html>
