function validerConnexion() {
    var email = document.querySelector("input[type='email']").value;
    var mdp = document.querySelector("input[type='password']").value;

    if(email == ""){
        alert("Veuillez saisir votre email");
        return false;
    }

    if(mdp == ""){
        alert("Veuillez saisir votre mot de passe");
        return false;
    }

    return true; 
}

function validerCompte() {
    var nom = document.querySelector("input[name='nom']").value;
    var email = document.querySelector("input[name='email']").value;
    var dateNaissance = document.querySelector("input[name='date_naissance']").value;
    var tel = document.querySelector("input[name='telephone']").value;

    if (nom == "") {
        alert("Le nom est obligatoire.");
        return false;
    }

    if (email == "") {
        alert("L'email est obligatoire.");
        return false;
    }

    if (email.indexOf("@") === -1) {
        alert("Email incorrect.");
        return false;
    }

    if (dateNaissance == "") {
        alert("La date de naissance est obligatoire.");
        return false;
    }

    if (tel == "") {
        alert("Le numéro de téléphone est obligatoire.");
        return false;
    }

    if (isNaN(tel)) {
        alert("Le numéro de téléphone doit contenir uniquement des chiffres.");
        return false;
    }

    if (tel.length < 8) {
        alert("Le numéro doit contenir au moins 8 chiffres.");
        return false;
    }

    alert("Formulaire validé !");
    return true;
}


function validerInscription() {
    var nom = document.querySelector("input[name='nom']").value;
    var prenom = document.querySelector("input[name='prenom']").value;
    var email = document.querySelector("input[name='email']").value;
    var mdp = document.querySelector("input[name='motdepasse']").value;
    var cmdp = document.querySelector("input[name='cmotdepasse']").value;

    // Réinitialiser les messages
    document.getElementById("nomError").innerText = "";
    document.getElementById("prenomError").innerText = "";
    document.getElementById("emailError").innerText = "";
    document.getElementById("mdpError").innerText = "";
    document.getElementById("cmdpError").innerText = "";

    var valide = true;

    // Nom
    if (nom == "") {
        document.getElementById("nomError").innerText = "Le nom est obligatoire.";
        valide = false;
    } else if (!/^[a-zA-Z]{2,}$/.test(nom)) {
        document.getElementById("nomError").innerText = "Le nom doit contenir au moins 2 lettres et uniquement des lettres.";
        valide = false;
    }

    // Prénom
    if (prenom == "") {
        document.getElementById("prenomError").innerText = "Le prénom est obligatoire.";
        valide = false;
    } else if (!/^[a-zA-Z]{2,}$/.test(prenom)) {
        document.getElementById("prenomError").innerText = "Le prénom doit contenir au moins 2 lettres et uniquement des lettres.";
        valide = false;
    }

    // Email
    var regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email == "") {
        document.getElementById("emailError").innerText = "L'email est obligatoire.";
        valide = false;
    } else if (!regexEmail.test(email)) {
        document.getElementById("emailError").innerText = "Email invalide.";
        valide = false;
    }

    // Mot de passe
    var regexMdp = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/;
    if (mdp == "") {
        document.getElementById("mdpError").innerText = "Veuillez saisir un mot de passe.";
        valide = false;
    } else if (!regexMdp.test(mdp)) {
        document.getElementById("mdpError").innerText = "Le mot de passe doit contenir au moins 6 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
        valide = false;
    }

    // Confirmation mot de passe
    if (cmdp == "") {
        document.getElementById("cmdpError").innerText = "Veuillez confirmer le mot de passe.";
        valide = false;
    } else if (mdp !== cmdp) {
        document.getElementById("cmdpError").innerText = "Les mots de passe ne correspondent pas.";
        valide = false;
    }

    return valide;
}



function validerMdpOublie() {
    var mdp = document.getElementById("motdepasse").value;
    var cmdp = document.getElementById("cmotdepasse").value;

    if (mdp == "") {
        alert("Veuillez saisir un nouveau mot de passe.");
        return false;
    }

    if (mdp.length < 6) {
        alert("Le mot de passe doit faire au moins 6 caractères.");
        return false;
    }

    if (cmdp == "") {
        alert("Veuillez confirmer votre mot de passe.");
        return false;
    }

    if (mdp !== cmdp) {
        alert("Les mots de passe ne correspondent pas.");
        return false;
    }

    alert("Mot de passe mis à jour !");
    return true;
}

function validerChangementMdp() {
    var ancien = document.querySelector("input[name='ancien']").value;
    var nouveau = document.querySelector("input[name='nouveau']").value;
    var confirmer = document.querySelector("input[name='confirmer']").value;

    if (ancien == "") {
        alert("Veuillez saisir l'ancien mot de passe.");
        return false;
    }

    if (nouveau == "") {
        alert("Veuillez saisir le nouveau mot de passe.");
        return false;
    }

    if (nouveau.length < 6) {
        alert("Le nouveau mot de passe doit contenir au moins 6 caractères.");
        return false;
    }

    if (confirmer == "") {
        alert("Veuillez confirmer le mot de passe.");
        return false;
    }

    if (nouveau !== confirmer) {
        alert("Les mots de passe ne correspondent pas.");
        return false;
    }

    alert("Mot de passe changé avec succès !");
    return true;
}
