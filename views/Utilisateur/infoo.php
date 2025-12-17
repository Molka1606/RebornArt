
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

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../model/config.php';
require_once __DIR__ . '/../../model/notif.php';


$notifications = [];
$notifCount = 0;

if (isset($_SESSION['user']['id'])) {
    $db = config::getConnexion();
    $notif = new Notif($db);

    $notifications = $notif->getUnreadByUser($_SESSION['user']['id']);
    $notifCount = count($notifications);
}
?>


<style>
.notif-icon {
    position: relative;
    display: inline-block;
    font-size: 18px;   /* taille cloche */
}

.notif-badge {
    position: absolute;
    top: -6px;
    right: -6px;
    min-width: 14px;
    height: 14px;
    line-height: 14px;
    background: red;
    color: white;
    border-radius: 50%;
    font-size: 10px;   /* 🔥 taille du "1" */
    font-weight: bold;
    text-align: center;
    padding: 0 3px;
}

.notif-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 30px;
    width: 300px;
    background: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-radius: 5px;
    z-index: 1000;
}

.notif-item {
    padding: 10px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}

.notif-item:hover {
    background: #f5f5f5;
}

.notif-item.read {
    opacity: 0.6;
}

.notif-empty {
    padding: 10px;
    text-align: center;
    color: gray;
}


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
<a href="../indexx.php" class="logo"><h4>Reborn<span>Art</span></h4></a>
<ul class="nav">
<li class="scroll-to-section"><a href="#top" class="active">Accueil</a></li>
<li class="scroll-to-section"><a href="#about">A propos nous</a></li>
<li class="scroll-to-section"><a href="#portfolio">Creations</a></li>
<li class="scroll-to-section"><a href="../view/front/articles.php">Blog</a></li>
<li class="scroll-to-section notif-wrapper">
    <a href="#" onclick="toggleNotif()" class="notif-icon">
        🔔
        <?php if (!empty($notifCount) && $notifCount > 0): ?>
            <span class="notif-badge"><?= $notifCount ?></span>
        <?php endif; ?>
    </a>

    <!-- Dropdown notifications -->
    <div class="notif-dropdown" id="notifDropdown">
        <?php if (empty($notifications)): ?>
            <p class="notif-empty">Aucune notification</p>
        <?php else: ?>
            <?php foreach ($notifications as $n): ?>
                <div class="notif-item <?= $n['is_read'] ? 'read' : '' ?>">
                    <strong><?= htmlspecialchars($n['username']) ?></strong>
                    <?= htmlspecialchars($n['message']) ?>
                    <br>
                    <small><?= $n['date_created'] ?></small>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</li>

<li class="scroll-to-section" id="userMenu">
    <?php echo $userMenuContent; ?>
</li>
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
                    <div class="col-md-6">
                        <label>Téléphone</label>
                        <input type="text" name="telephone" 
                            value="<?php echo isset($user['telephone']) ? htmlspecialchars($user['telephone']) : ''; ?>">
                    </div>

                    <div class="col-md-6">
                        <label>Date de naissance</label>
                        <input type="date" name="date_naissance" 
                            value="<?php echo isset($user['date_naissance']) ? htmlspecialchars($user['date_naissance']) : ''; ?>">
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
<p>2025 RebornArt - Tous droits réservés</p>
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
<!-- ================= CHATBOT REBORNART ================= -->

<!-- Bouton flottant -->
<button id="chatbot-btn">💬</button>

<!-- Fenêtre du chatbot -->
<div id="chatbot-window" style="display:none; flex-direction:column;">
    <div id="chatbot-messages"></div>
    <!-- ✅ Boutons rapides + Effacer -->
    <div id="chatbot-quick">
      <button onclick="quickSend('Donne-moi une idée de recyclage')"> Idée recyclage</button>
      <button onclick="quickSend('Quels métiers propose RebornArt ?')"> Métiers</button>
      <button onclick="quickSend('Comment gérer mon compte ?')"> Aide compte</button>
      <button id="clear-history" class="clear-btn">Effacer</button>
    </div>

    <!-- Messages -->
    

    <!-- Input -->
    <div id="chatbot-input">
        <input type="text" id="chatbot-text" placeholder="Écrire un message...">
        <button id="chatbot-send">➤</button>
        <button id="chatbot-voice">🎤</button>
    </div>
</div>
<!-- CSS & JS du Chatbot -->
<link rel="stylesheet" href="../assets/css/chatbot.css">
<script src="../assets/js/chatbot.js"></script>
<script>
function toggleNotif() {
    const box = document.getElementById('notifDropdown');
    box.style.display = box.style.display === 'block' ? 'none' : 'block';
}

// fermer si clic ailleurs
document.addEventListener('click', function(e) {
    const notif = document.getElementById('notifDropdown');
    const icon = document.querySelector('.notif-icon');
    if (notif && !notif.contains(e.target) && !icon.contains(e.target)) {
        notif.style.display = 'none';
    }
});
</script>

</body>
</html>
