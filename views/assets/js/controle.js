function validerProfil() {
    var nom = document.getElementById("nom").value;
    var email = document.getElementById("email").value;
    var mdp = document.getElementById("password").value;
    var nouveauMdp = document.getElementById("newpassword").value;

    if(nom.length < 3){
        alert("Le nom est trop court");
        return false;
    }
    var motifEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(!motifEmail.test(email)){
        alert("Adresse email invalide");
        return false;
    }
    if(mdp === ""){
        alert("Le mot de passe actuel est requis");
        return false;
    }
    if(nouveauMdp !== "" && nouveauMdp.length < 6){
        alert("Le nouveau mot de passe doit contenir au moins 6 caractères");
        return false;
    }
    return true;
}

function validermdp() {
    var mdp=document.getElementById("motdepasse").value;
    var cmdp=document.getElementById("cmotdepasse").value;
    if(mdp.length < 6){
        alert("Le mot de passe doit contenir au moins 6 caractères");
        return false;
    }
    if(mdp !== cmdp){
        alert("Les mots de passe ne correspondent pas");
        return false;
    }
    return true;
}

function login(){
    var email = document.getElementById("email").value;
    var mdp = document.getElementById("motdepasse").value;
    var motifEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(!motifEmail.test(email)){
        alert("Adresse e-mail invalide");
        return false;
    }
    if(mdp.length < 6){
        alert("Le mot de passe doit contenir au moins 6 caractères");
        return false;
    }

    return true;
}

