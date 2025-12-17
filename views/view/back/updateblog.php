<?php
require_once __DIR__ . '/../../../model/config.php'; // connexion PDO

if(!isset($_GET['id'])) {
    die("ID manquant");
}

$id = (int)$_GET['id'];
$db = config::getConnexion();


// Récupérer l'article
$stmt = $db->prepare("SELECT * FROM blog WHERE id=:id");
$stmt->execute([':id' => $id]);
$blog = $stmt->fetch();

if(!$blog) {
    die("Article introuvable");
}

// Traitement du formulaire
if(isset($_POST['submit'])) {
    $titre = $_POST['titre'];
    $contenu = $_POST['contenu'];
    $date_pub = $_POST['date_publication'];

    $stmt = $db->prepare("UPDATE blog SET titre=:titre, contenu=:contenu, date_pub=:date_pub WHERE id=:id");
    $stmt->execute([
        ':titre' => $titre,
        ':contenu' => $contenu,
        ':date_pub' => $date_pub,
        ':id' => $id
    ]);

    header("Location: showblog.php");
    exit();
}
?>

<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Modifier un article - Back-office</title>

```
<!-- Fonts & Styles -->
<link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
<link href="css/sb-admin-2.min.css" rel="stylesheet">
```

</head>
<body id="page-top">

<!-- Page Wrapper -->

<div id="wrapper">

```
<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">RebornArt</div>
    </a>
    <hr class="sidebar-divider my-0">
    <li class="nav-item">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>
    <hr class="sidebar-divider">
    <div class="sidebar-heading">Gestion</div>
    <li class="nav-item">
        <a class="nav-link" href="showblog.php">
            <i class="fas fa-fw fa-newspaper"></i>
            <span>Articles</span></a>
    </li>
    <li class="nav-item active">
        <a class="nav-link" href="../front/addblog.php">
            <i class="fas fa-fw fa-plus"></i>
            <span>Ajouter Article</span></a>
    </li>
    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End of Sidebar -->

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>
        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800">Modifier l'article</h1>

            <div class="card shadow mb-4">
                <div class="card-body">
                    <form method="post">
                        <div class="form-group">
                            <label for="titre">Titre :</label>
                            <input type="text" class="form-control" id="titre" name="titre"
                                   value="<?= htmlspecialchars($blog['titre']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="contenu">Contenu :</label>
                            <textarea class="form-control" id="contenu" name="contenu" rows="8" required><?= htmlspecialchars($blog['contenu']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="date_publication">Date de publication :</label>
                            <input type="date" class="form-control" id="date_publication" name="date_publication"
                                   value="<?= htmlspecialchars($blog['date_pub']); ?>" required>
                        </div>

                        <button type="submit" name="submit" class="btn btn-primary">Mettre à jour</button>
                        <a href="showblog.php" class="btn btn-secondary">Annuler</a>
                    </form>
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
                <span>Copyright &copy; RebornArt 2025</span>
            </div>
        </div>
    </footer>
    <!-- End of Footer -->

</div>
<!-- End of Content Wrapper -->
```

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->

<a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

<!-- JS Scripts -->

<script src="vendor/jquery/jquery.min.js"></script>

<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<script src="js/sb-admin-2.min.js"></script>

</body>
</html>
