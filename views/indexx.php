<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (isset($_SESSION['user'])) {
    $nom = htmlspecialchars($_SESSION['user']['nom']);
    $prenom = htmlspecialchars($_SESSION['user']['prenom']);
} else {
    $nom = $prenom = null;
}

$notifCount = 0;

if (isset($_SESSION['user']['id'])) {
    require_once __DIR__ . '/../model/config.php';

    $db = config::getConnexion();
    $stmt = $db->prepare("
        SELECT COUNT(*) 
        FROM notifications 
        WHERE id_user = :id_user AND is_read = 0
    ");
    $stmt->execute([
        'id_user' => $_SESSION['user']['id']
    ]);

    $notifCount = $stmt->fetchColumn();
    $notifications = [];

if (isset($_SESSION['user']['id'])) {

    // 🔔 notifications récentes (liste)
    $stmt = $db->prepare("
        SELECT * FROM notifications
        WHERE id_user = :id_user
        ORDER BY date_created DESC
        LIMIT 5
    ");
    $stmt->execute([
        'id_user' => $_SESSION['user']['id']
    ]);

    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
?>



<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <title>RebornArt - FrontOffice</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-space-dynamic.css">
    <link rel="stylesheet" href="assets/css/animated.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="../../css/signup.css">

<!--
    
TemplateMo 562 Space Dynamic

https://templatemo.com/tm-562-space-dynamic

-->
  </head>

<body>

  <!-- ***** Preloader Start ***** -->
  <div id="js-preloader" class="js-preloader">
    <div class="preloader-inner">
      <span class="dot"></span>
      <div class="dots">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
  </div>
  <!-- ***** Preloader End ***** -->

  <!-- ***** Header Area Start ***** -->
  <header class="header-area header-sticky wow slideInDown" data-wow-duration="0.75s" data-wow-delay="0s">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <nav class="main-nav">
            <!-- ***** Logo Start ***** -->
            <a href="indexx.php" class="logo">
              <h4>Reborn<span>Art</span></h4>
            </a>
            <!-- ***** Logo End ***** -->
            <!-- ***** Menu Start ***** -->
            <ul class="nav">
              <li class="scroll-to-section"><a href="#top" class="active">Accueil</a></li>
              <li class="scroll-to-section"><a href="#about">A propos nous</a></li>
              <li class="scroll-to-section"><a href="#portfolio">Creations</a></li>
              <li class="scroll-to-section"><a href="../views/view/front/articles.php">Blog</a></li> 
              <li style="position: relative;">
                  <a href="#" id="notifIcon" onclick="toggleNotif(); return false;">
                      🔔
                      <?php if ($notifCount > 0): ?>
                          <span class="notif-badge"><?= $notifCount ?></span>
                      <?php endif; ?>
                  </a>

                  <!-- 🔔 LISTE NOTIFICATIONS -->
                  <div id="notifDropdown" class="notif-dropdown">
                      <?php if (empty($notifications)): ?>
                          <p class="notif-empty">Aucune notification</p>
                      <?php endif; ?>

                    <?php foreach ($notifications as $n): ?>
                        <div class="notif-item <?= $n['is_read'] ? 'read' : '' ?>"
                            onclick="window.location.href='../views/view/front/detailartc.php?id=<?= (int)$n['reference_id'] ?>'">
                            
                            <strong><?= htmlspecialchars($n['username']) ?></strong>
                            <?= htmlspecialchars($n['message']) ?>
                            <br>
                            <small><?= $n['date_created'] ?></small>
                        </div>
                    <?php endforeach; ?>

                  </div>
              </li>


              <li class="main-red-button" id="userMenu">
              <?php if($nom && $prenom): ?>
                  <a href="#" onclick="return false;"><?php echo $nom . ' ' . $prenom; ?></a>
                  <ul class="listee">
                      <li><a href="Utilisateur/infoo.php">Profil</a></li>
                      <li><a href="Utilisateur/logout.php">Déconnexion</a></li>
                  </ul>
              <?php else: ?>
                  <a href="Utilisateur/signIn.html" id="loginBtn">Se connecter</a>
              <?php endif; ?>
              </li>


            </ul>
              </li> 
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </header>
  <!-- ***** Header Area End ***** -->

  <div class="main-banner wow fadeIn" id="top" data-wow-duration="1s" data-wow-delay="0.5s">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="row">
            <div class="col-lg-6 align-self-center">
              <div class="left-content header-text wow fadeInLeft" data-wow-duration="1s" data-wow-delay="1s">
                <h6>Bienvenue chez RebornArt</h6>
                <h2>Nous transformons les <em>déchets</em> en <span>art</span></h2>
                <p>RebornArt donne une seconde vie aux matériaux recyclés à travers la créativité et le design. Nous transformons des objets abandonnés en œuvres uniques et écologiques qui inspirent le changement.</p>


                <!-- <form id="search" action="#" method="GET">
                  <fieldset>
                    <input type="address" name="address" class="email" placeholder="Your website URL..." autocomplete="on" required>
                  </fieldset>
                  <fieldset>
                    <button type="submit" class="main-button">Analyze Site</button>
                  </fieldset>
                </form> -->
              </div>
            </div>
            <div class="col-lg-6">
              <div class="right-image wow fadeInRight" data-wow-duration="1s" data-wow-delay="0.5s">
                <img src="assets/images/recycling.jpg" alt="team meeting">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="about" class="about-us section">
    <div class="container">
      <div class="row">
        <div class="col-lg-4">
          <div class="left-image wow fadeIn" data-wow-duration="1s" data-wow-delay="0.2s">
            <img src="assets/images/about-left-image.png" alt="person graphic">
          </div>
        </div>
        <div class="col-lg-8 align-self-center">
          <div class="services">
            <div class="row">
              <div class="col-lg-6">
                <div class="item wow fadeIn" data-wow-duration="1s" data-wow-delay="0.5s">
                  <div class="icon">
                    <img src="assets/images/service-icon-01.png" alt="reporting">
                  </div>
                  <div class="right-text">
                    <h4>Donner une Seconde Vie</h4>
                    <p>Offrez une nouvelle utilité à vos objets.</p>
                </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="item wow fadeIn" data-wow-duration="1s" data-wow-delay="0.7s">
                  <div class="icon">
                    <img src="assets/images/service-icon-02.png" alt="">
                  </div>
                   <div class="right-text">
                      <h4>Communauté Responsable</h4>
                      <p>Rejoignez un réseau d’utilisateurs engagés.</p>
                </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="item wow fadeIn" data-wow-duration="1s" data-wow-delay="0.9s">
                  <div class="icon">
                    <img src="assets/images/service-icon-03.png" alt="">
                  </div>
                 <div class="right-text">
                  <h4>Inspiration & Créativité</h4>
                  <p>Découvrez des idées originales pour réutiliser vos anciens objets.</p>
                </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="item wow fadeIn" data-wow-duration="1s" data-wow-delay="1.1s">
                  <div class="icon">
                    <img src="assets/images/service-icon-04.png" alt="">
                  </div>
                   <div class="right-text">
                  <h4>Recyclage Assisté</h4>
                  <p>Notre IA vous suggère des idées personnalisées pour une seconde vie.</p>
                </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>



  <div id="portfolio" class="our-portfolio section">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 offset-lg-3">
          <div class="section-heading  wow bounceIn" data-wow-duration="1s" data-wow-delay="0.2s">
            <h2>Découvrez nos <em>créations</em> uniques &amp; ce que nous <span>proposons</span></h2>

          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-3 col-sm-6">
          <a href="#">
            <div class="item wow bounceInUp" data-wow-duration="1s" data-wow-delay="0.3s">
              <div class="hidden-content">
                <h4>SEO Analysis</h4>
                <p>Lorem ipsum dolor sit ameti ctetur aoi adipiscing eto.</p>
              </div>
              <div class="showed-content">
                <img src="assets/images/oeufs.png" alt="">
              </div>
            </div>
          </a>
        </div>
        <div class="col-lg-3 col-sm-6">
          <a href="#">
            <div class="item wow bounceInUp" data-wow-duration="1s" data-wow-delay="0.4s">
              <div class="hidden-content">
                <h4>Lampe unique</h4>
                <p>Une lampe unique fabriquée à partir de bouteilles en verre recyclées — entre art et durabilité.</p>
              </div>
              <div class="showed-content">
                <img src="assets/images/22.jpg" alt="">
              </div>
            </div>
          </a>
        </div>
        <div class="col-lg-3 col-sm-6">
          <a href="#">
            <div class="item wow bounceInUp" data-wow-duration="1s" data-wow-delay="0.5s">
              <div class="hidden-content">
                <h4>Performance Tests</h4>
                <p>Lorem ipsum dolor sit ameti ctetur aoi adipiscing eto.</p>
              </div>
              <div class="showed-content">
                <img src="assets/images/oeufs.png" alt="">
              </div>
            </div>
          </a>
        </div>
        <div class="col-lg-3 col-sm-6">
          <a href="#">
            <div class="item wow bounceInUp" data-wow-duration="1s" data-wow-delay="0.6s">
              <div class="hidden-content">
                <h4>Data Analysis</h4>
                <p>Lorem ipsum dolor sit ameti ctetur aoi adipiscing eto.</p>
              </div>
              <div class="showed-content">
                <img src="assets/images/2.jpg" alt="">
              </div>
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>


  <div id="contact" class="contact-us section">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 align-self-center wow fadeInLeft" data-wow-duration="0.5s" data-wow-delay="0.25s">
<div class="section-heading">
  <h2>N’hésitez pas à nous contacter pour donner vie à vos idées créatives</h2>
  <p>Vous avez une question, une collaboration à proposer ou un projet artistique à base de recyclage ? RebornArt est à votre écoute.</p>
  <div class="phone-info">
    <h4>Pour toute demande, contactez-nous :
      <span>
        <i class="fa fa-phone"></i>
        <a href="#">+216 72 589 479</a>
      </span>
    </h4>
  </div>
</div>

        </div>
        <div class="col-lg-6 wow fadeInRight" data-wow-duration="0.5s" data-wow-delay="0.25s">
          <form id="contact" action="" method="post">
            <div class="row">
              <div class="col-lg-6">
                <fieldset>
                  <input type="name" name="name" id="name" placeholder="Nom" autocomplete="on" >
                </fieldset>
              </div>
              <div class="col-lg-6">
                <fieldset>
                  <input type="surname" name="surname" id="surname" placeholder="Prénom" autocomplete="on" >
                </fieldset>
              </div>
              <div class="col-lg-12">
                <fieldset>
                  <input type="text" name="email" id="email" pattern="[^ @]*@[^ @]*" placeholder="Votre Email" >
                </fieldset>
              </div>
              <div class="col-lg-12">
                <fieldset>
                  <textarea name="message" type="text" class="form-control" id="message" placeholder="Message" ></textarea>  
                </fieldset>
              </div>
              <div class="col-lg-12">
                <fieldset>
                  <button type="submit" id="form-submit" class="main-button ">Envoyer Message</button>
                </fieldset>
              </div>
            </div>
            <div class="contact-dec">
              <img src="assets/images/contact-decoration.png" alt="">
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <footer>
    <div class="container">
      <div class="row">
        <div class="col-lg-12 wow fadeIn" data-wow-duration="1s" data-wow-delay="0.25s">
          <p>2025 RebornArt - Tous droits réservés 
        </div>
      </div>
    </div>
  </footer>
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/owl-carousel.js"></script>
  <script src="assets/js/animation.js"></script>
  <script src="assets/js/imagesloaded.js"></script>
  <script src="assets/js/templatemo-custom.js"></script>

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

#userMenu {
  position: relative;
  display: inline-block;
}

#userMenu .listee {
  display: none;
  position: absolute;
  right: 0;
  background-color: #f9f9f9;
  min-width: 150px;
  box-shadow: 0 8px 16px rgba(0,0,0,0.2);
  padding: 12px 16px;
  list-style: none;
}

#userMenu:hover .listee {
  display: block;
}

</style>

<!-- Chatbot Button -->
<button id="chatbot-btn">💬</button>

<!-- Chatbot Window -->
<div id="chatbot-window" style="display:none; flex-direction:column;">
    <div id="chatbot-messages"></div>
    <div id="chatbot-quick">
      <button onclick="quickSend('Donne-moi une idée de recyclage')"> Idée recyclage</button>
      <button onclick="quickSend('Quels métiers propose RebornArt ?')"> Métiers</button>
      <button onclick="quickSend('Comment gérer mon compte ?')"> Aide compte</button>

      <button id="clear-history" class="clear-btn">Effacer</button>
    </div>

    <div id="chatbot-input">
        <input type="text" id="chatbot-text" placeholder="Écrire un message...">
        <button id="chatbot-send">➤</button>
        <button id="chatbot-voice">🎤</button>



    </div>
</div>

<!-- Chatbot Files -->
<link rel="stylesheet" href="assets/css/chatbot.css">
<script src="assets/js/chatbot.js"></script>
<script>
function toggleNotif() {
    const box = document.getElementById('notifDropdown');
    box.style.display = (box.style.display === 'block') ? 'none' : 'block';
}

// fermer si clic ailleurs
document.addEventListener('click', function(e) {
    const notif = document.getElementById('notifDropdown');
    const icon = document.getElementById('notifIcon');
    if (!notif.contains(e.target) && !icon.contains(e.target)) {
        notif.style.display = 'none';
    }
});
</script>

</body>
</html>
