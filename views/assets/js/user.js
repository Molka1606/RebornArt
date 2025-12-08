function validerConnexion(event) {
    var email = document.getElementById("emailIn").value;
    var mdp = document.getElementById("motdepasseIn").value;

    document.getElementById("emailErrorIn").innerText = "";
    document.getElementById("mdpErrorIn").innerText = "";

    let valid = true;

    if (email === "") {
        document.getElementById("emailErrorIn").innerText = "Veuillez saisir votre email.";
        valid = false;
    }

    if (mdp === "") {
        document.getElementById("mdpErrorIn").innerText = "Veuillez saisir votre mot de passe.";
        valid = false;
    }

    if (!valid) {
        event.preventDefault(); // Empêche l'envoi si le formulaire est invalide
    }

    return valid;
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
    var regexMdp  = /^[A-Za-z0-9]{4,}$/; // Minimum 6 caractères, lettres + chiffres

    if (mdp === "") {
        document.getElementById("mdpError").innerText = "Veuillez saisir un mot de passe.";
        valide = false;
    } 
    else if (!regexMdp.test(mdp)) {
        document.getElementById("mdpError").innerText = "Le mot de passe doit contenir au moins 6 caractères (lettres et chiffres).";
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