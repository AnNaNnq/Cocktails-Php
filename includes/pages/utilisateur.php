<?php
session_start();
include("fonctions.php");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Connexion</title>
    <meta charset="utf-8" />
    <style>
        header {
            border-style: groove;
            text-align: right;
        }
    </style>
</head>

<body>
    <header>
        <?php
        if (isset($_POST['connexion'])) {
            if (isset($_POST['login']) && isset($_POST['mdp'])) {
                seConnecter();
            }
        }
        if (isset($_POST['deconnecter'])) {
            $_SESSION["isConnected"] = false;
        }
        if (isset($_SESSION["isConnected"]) && $_SESSION["isConnected"] == true) {
        ?>
            <form method="post">
                    <input type="submit" name="profil" value="Profil"/>
                    <input type="submit" name="deconnecter" value="Se déconnecter"/>
                    </form>
        <?php
        echo $_SESSION["Login"];
        } else {
        ?>
            <form method="post">
                Login <input type="text" name="login" />
                Mot de passe <input type="text" name="mdp" />
                <input type="submit" name="connexion" value="Connexion" />
                <input type="submit" name="inscrire" value="Inscrire" />
            </form>
        <?php
        }
        ?>
    </header>
    <?php
    if (isset($_POST['inscrire'])) {
        include("inscrire.php");
    }
    if (isset($_POST['valider'])) {
        ajouterUser();
    }
    if (isset($_POST['profil'])) {
        include("profil.php");
    }
    if (isset($_POST['modifier'])) {
        changerProfil();
    }
    ?>

</body>

</html>