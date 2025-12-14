<?php
require __DIR__ . "/../../controller/blogcontroller.php";
require __DIR__ . "/../../controller/commentcontroller.php";
require __DIR__ . "/../../controller/BlogStatsController.php";

$blogC = new BlogController();
$commentC = new CommentaireController();
$statsC = new BlogStatsController();

$liste = $blogC->fetchBlog(); // tous les articles
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Back-office - Liste des articles</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
            <div class="sidebar-brand-icon rotate-n-15">
                <i class="fas fa-laugh-wink"></i>
            </div>
            <div class="sidebar-brand-text mx-3">RebornArt</div>
        </a>
        <hr class="sidebar-divider my-0">

        <li class="nav-item active">
            <a class="nav-link" href="showblog.php">
                <i class="fas fa-fw fa-newspaper"></i>
                <span>Articles</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="showcomment.php">
                <i class="fas fa-fw fa-comments"></i>
                <span>Commentaires</span>
            </a>
        </li>
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
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">Admin</span>
                            <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Liste des articles</h1>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Articles disponibles</h6>
                    </div>
                    <div class="card-body">
                        <?php if(empty($liste)): ?>
                            <p>Aucun article disponible.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Titre</th>
                                            <th>Contenu</th>
                                            <th>Date</th>
                                            <th>Vues</th>
                                            <th>Likes</th>
                                            <th>Dislikes</th>
                                            <th>Commentaires</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($liste as $blog): 
                                            // Récupérer stats
                                            $stats = $statsC->getStatsForBlog($blog['id']);
                                            // Récupérer commentaires
                                            $comments = $commentC->fetchCommentairesByBlog($blog['id']);
                                        ?>
                                        <tr>
                                            <td><?= $blog['id']; ?></td>
                                            <td><?= htmlspecialchars($blog['titre']); ?></td>
                                            <td><?= htmlspecialchars($blog['contenu']); ?></td>
                                            <td><?= $blog['date_pub']; ?></td>
                                            <td><?= $stats['views']; ?></td>
                                            <td><?= $stats['likes']; ?></td>
                                            <td><?= $stats['dislikes']; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalComments<?= $blog['id']; ?>">
                                                    Voir (<?= count($comments); ?>)
                                                </button>
                                            </td>
                                            <td>
                                                <a href="updateblog.php?id=<?= $blog['id']; ?>" class="btn btn-warning btn-sm">Modifier</a>
                                                <a href="deleteblog.php?id=<?= $blog['id']; ?>" class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Voulez-vous vraiment supprimer cet article ?');">Supprimer</a>
                                            </td>
                                        </tr>

                                        <!-- Modal commentaires -->
                                        <div class="modal fade" id="modalComments<?= $blog['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel<?= $blog['id']; ?>" aria-hidden="true">
                                          <div class="modal-dialog modal-dialog-scrollable" role="document">
                                            <div class="modal-content">
                                              <div class="modal-header">
                                                <h5 class="modal-title" id="modalLabel<?= $blog['id']; ?>">Commentaires pour "<?= htmlspecialchars($blog['titre']); ?>"</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                  <span aria-hidden="true">&times;</span>
                                                </button>
                                              </div>
                                              <div class="modal-body">
                                                  <?php if(!empty($comments)): ?>
                                                      <?php foreach($comments as $c): ?>
                                                          <div class="border p-2 mb-2">
                                                              <strong><?= htmlspecialchars($c['nom']); ?></strong> le <?= $c['date_pub']; ?><br>
                                                              <?= nl2br(htmlspecialchars($c['contenu'])); ?>
                                                              <br>
                                                              <a href="../../controller/commentcontroller.php?action=delete&id=<?= $c['id']; ?>&blog=<?= $blog['id']; ?>&back=showblog"
                                                                    class="btn btn-sm btn-danger mt-2"
                                                                    onclick="return confirm('Voulez-vous vraiment supprimer ce commentaire ?');">
                                                                    Supprimer
                                                              </a>

                                                              </a>
                                                          </div>
                                                      <?php endforeach; ?>
                                                  <?php else: ?>
                                                      <p>Aucun commentaire.</p>
                                                  <?php endif; ?>
                                              </div>
                                              <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                                              </div>
                                            </div>
                                          </div>
                                        </div>

                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
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

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/sb-admin-2.min.js"></script>

</body>
</html>
