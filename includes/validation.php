<?php
if (isset($_POST['inscrire_utilisateur'])) {
    ajouterUser();
}
if (isset($_POST['modifier_profil'])) {
    changerProfil();
}
if(isset($_GET["page"]) && $_GET["page"] == "sedeconnecter"){
    unset($_SESSION["isConnected"]);
    unset($_SESSION["login"]);
}

if (isset($_POST['connexion'])) {
    if (isset($_POST['login']) && isset($_POST['mdp'])) {
        seConnecter();
    }
}
?>