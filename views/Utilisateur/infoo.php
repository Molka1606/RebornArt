
<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: signIn.html");
    exit;
}
$user = $_SESSION['user'];

$userMenuContent = '';

if (isset($_SESSION['user'])) {
    $nom = $_SESSION['user']['nom'];
    $prenom = $_SESSION['user']['prenom'];

    $userMenuContent = '
        <a href="#" onclick="return false;">'.$nom.' '.$prenom.'</a>
        <ul class="listee">
            <li><a href="infoo.php">Profil</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    ';
} else {
    $userMenuContent = '<a href="views/Utilisateur/signIn.html">Se connecter</a>';
}
?>
<style>
#userMenu > a { display:inline-block; background-color:#28a745; color:white !important; padding:6px 12px; border-radius:6px; font-size:14px; font-weight:4500; text-decoration:none; transition:0.2s ease-in-out; }
#userMenu > a:hover { background-color:#218838; }
#userMenu .listee li { width:100%; }
#userMenu .listee li a { display:block; padding:8px 14px; font-size:14px; color:#333; text-decoration:none; transition:0.2s; }
#userMenu .listee li a:hover { background-color:#f1f1f1; }
#userMenu:hover .listee { display:block; }
#userMenu { position:relative; display:inline-block; }
#userMenu .listee { display:none; position:absolute; right:0; background-color:#f9f9f9; min-width:150px; box-shadow:0 8px 16px rgba(0,0,0,0.2); padding:12px 16px; list-style:none; }
#userMenu:hover .listee { display:block; }
</style>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>RebornArt - FrontOffice</title>
<link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/fontawesome.css">
<link rel="stylesheet" href="../assets/css/templatemo-space-dynamic.css">
<link rel="stylesheet" href="../assets/css/animated.css">
<link rel="stylesheet" href="../assets/css/owl.css">
<link rel="stylesheet" href="../assets/css/signup.css">
</head>
<body>

<header class="header-area header-sticky wow slideInDown" data-wow-duration="0.75s" data-wow-delay="0s">
<div class="container"><div class="row"><div class="col-12">
<nav class="main-nav">
<a href="indexx.php" class="logo"><h4>Reborn<span>Art</span></h4></a>
<ul class="nav">
<li class="scroll-to-section"><a href="#top" class="active">Accueil</a></li>
<li class="scroll-to-section"><a href="#about">A propos nous</a></li>
<li class="scroll-to-section"><a href="#portfolio">Creations</a></li>
<li class="scroll-to-section"><a href="#blog">Blog</a></li>
<li class="scroll-to-section" id="userMenu"><?php echo $userMenuContent; ?></li>
</ul>
</nav>
</div></div></div>
</header>

<div class="profile-container">

    <div class="left-card">
        <img src="<?php echo isset($user['photo']) && $user['photo'] != '' ? $user['photo'] : 'https://cdn-icons-png.flaticon.com/512/149/149071.png'; ?>" 
             alt="photo" 
             style="width:150px; height:150px; border-radius:50%; object-fit:cover;">

        <h4>Mon Profil</h4>
        <br>

        <!-- ✔️ INPUT IMAGE UNIQUE -->
        <label class="btn-main" style="cursor:pointer;">
            Choisir une photo
            <input type="file" id="profileImage" name="profileImage" form="updateForm" accept="image/*" style="display:none;">
        </label>

        <form method="GET" action="deleteuser.php" onsubmit="return confirm('Voulez-vous vraiment supprimer votre compte ?');">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
            <button type="submit" class="btn-main">Supprimer</button>
        </form>
            <form method="POST" action="desactiver_compte.php"
        onsubmit="return confirm('Voulez-vous vraiment désactiver votre compte ?');">
        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
        <button type="submit" class="btn-main" style="background-color:#c82333; margin-top:10px;">
            Désactiver mon compte
        </button>
    </form>

    </div>

    <div class="right-card">
        <h2>Profil</h2>

        <!-- ✔️ FORMULAIRE DU PROFIL AVEC ID POUR L’INPUT -->
        <form method="POST" action="updateUser.php" enctype="multipart/form-data" id="updateForm">
            <div class="row">
                <div class="col-md-6">
                    <label>Nom</label>
                    <input type="text" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label>Prénom</label>
                    <input type="text" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label>Mot de passe actuel</label>
                    <input type="password" name="current_password">
                </div>
                <div class="col-md-6">
                    <label>Nouveau mot de passe</label>
                    <input type="password" name="new_password">
                </div>
            </div>

            <button type="submit" class="btn-main mt-3">Modifier</button>
        </form>
    </div>
</div>

<footer>
<div class="container">
<div class="row">
<div class="col-lg-12 wow fadeIn" data-wow-duration="1s" data-wow-delay="0.25s">
<p>Copyright RebornArt 2025. All Rights Reserved.</p>
</div>
</div>
</div>
</footer>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/owl-carousel.js"></script>
<script src="../assets/js/animation.js"></script>
<script src="../assets/js/imagesloaded.js"></script>
<script src="../assets/js/templatemo-custom.js"></script>

<script>
    // ✔️ Aperçu instantané
    var profileImageInput = document.getElementById('profileImage');
    var profilePreview = document.querySelector('.left-card img');

    profileImageInput.addEventListener('change', function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                profilePreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>

</body>
</html>
