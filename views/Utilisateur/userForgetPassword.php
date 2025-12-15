<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body class="body-profil">

    <div class="box">
        <div class="form-box">

            <h1 class="htitre">Mot de passe oublié</h1>

            <input type="email" id="email" placeholder="Votre email ">

            <span id="msg" style="color:red; font-size:15px;"></span>

            <button onclick="sendCode()">Envoyer le code</button>

        </div>
                <div class="side-box">
            <div class="side"></div>
        </div>
    </div>

<script>
    function sendCode() {
        let email = document.getElementById("email").value;

        if (email.trim() === "") {
            document.getElementById("msg").innerText = "Veuillez entrer votre email.";
            return;
        }

        let formData = new FormData();
        formData.append("email", email);

        fetch("../../controller/userSendCode.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(data => {
            data = data.trim(); // <-- IMPORTANT !

            if (data === "success") {
                document.getElementById("msg").style.color = "green";
                document.getElementById("msg").innerText =
                    "Code envoyé ! Vérifiez votre email.";

                setTimeout(() => {
                    window.location.href = "../Utilisateur/userVerifyCode.php";
                }, 1500);
            }
            else if (data === "no_email") {
                document.getElementById("msg").innerText =
                    "Erreur : email non envoyé au serveur.";
            }
            else {
                document.getElementById("msg").innerText =
                    "Erreur d'envoi : " + data;
            }
        });
    }
</script>

</body>
</html>
