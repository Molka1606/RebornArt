<?php
session_start();

// Si aucune email n'a été vérifiée → retour à mot de passe oublié
if (!isset($_SESSION['email_verif'])) {
    header("Location: adminForgetPassword.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vérification du code</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body class="body-profil">
    <div class="box">
        <div class="form-box">

            <h1 class="htitre">Vérification du code</h1>

            <p>Un code a été envoyé à : <b><?php echo $_SESSION['email_verif']; ?></b></p>

            <input type="text" id="code" placeholder="Entrez le code">

            <span id="msg" style="color:red; font-size:15px;"></span>

            <button onclick="verify()">Vérifier</button>

        </div>
        <div class="side-box">
            <div class="side"></div>
        </div>
    </div>

    <script>
        function verify() {
            let code = document.getElementById("code").value;

            if (code.trim() === "") {
                document.getElementById("msg").innerText = "Veuillez entrer le code.";
                return;
            }

            let formData = new FormData();
            formData.append("code", code);

            fetch("../../controller/checkCode.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.text())
            .then(data => {

                if (data === "correct") {
                    document.getElementById("msg").style.color = "green";
                    document.getElementById("msg").innerText = "Code correct !";

                    setTimeout(() => {
                        window.location.href = "resetPassword.php";
                    }, 1000);
                } 
                else if (data === "wrong") {
                    document.getElementById("msg").innerText = "Code incorrect.";
                }
                else {
                    document.getElementById("msg").innerText = "Erreur : " + data;
                }
            });
        }
    </script>
</body>
</html>
