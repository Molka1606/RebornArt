<?php
require_once __DIR__ . '/../../controller/userController.php';
require_once __DIR__ . '/../../model/Utilisateur.php';

$userC = new userController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!empty($_POST['nom']) && !empty($_POST['prenom']) 
        && !empty($_POST['email']) && !empty($_POST['motdepasse']) && !empty($_POST['cmotdepasse'])) {

        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $email = trim($_POST['email']);
        $motdepasse = $_POST['motdepasse'];
        $cmotdepasse = $_POST['cmotdepasse'];

        // ✅ 1. Vérification mot de passe
        if ($motdepasse !== $cmotdepasse) {
            echo "<script>
                alert('Les mots de passe ne correspondent pas');
                window.location.href='signUp.html';
            </script>";
            exit;
        }

        // ✅ 2. Vérification email unique
        if ($userC->emailExists($email)) {
            echo "<script>
                alert('Cet email est déjà utilisé');
                window.location.href='signUp.html';
            </script>";
            exit;
        }

        // ✅ 3. Création de l’objet User (CORRECTE)
        $user = new User(
            null,
            $nom,
            $prenom,
            $email,
            $motdepasse,
            'user',
            null
        );

        // ✅ 4. Insertion en base
        $userC->Adduser($user);

        echo "<script>
            alert('Compte créé avec succès !');
            window.location.href='signIn.html';
        </script>";
        exit;
    }
}
?>
