<?php if (!(isset($_SESSION["isConnected"]) && $_SESSION["isConnected"] == true)) {
    ?>
    <form method="post" action="#">
        <div class="row">
            <div class="col">
                Login <input type="text" name="login"
                             class="form-control form-control-sm <?php echo isset($erreurs_formulaire['login']) ? 'is-invalid' : '' ?>"/>
                <?php if (isset($erreurs_formulaire['login'])) { ?>
                    <div id="validationServerUsernameFeedback" class="invalid-feedback">
                        <?php echo $erreurs_formulaire['login'] ?>
                    </div>
                <?php } ?>
            </div>
            <div class="col">
                Mot de passe <input type="password" name="mdp"
                                    class="form-control form-control-sm <?php echo isset($erreurs_formulaire['mdp']) ? 'is-invalid' : '' ?>"/>
                <?php if (isset($erreurs_formulaire['mdp'])) { ?>
                    <div id="validationServerUsernameFeedback" class="invalid-feedback">
                        <?php echo $erreurs_formulaire['mdp'] ?>
                    </div>
                <?php } ?>
            </div>
            <div class="col d-flex justify-content-between align-items-end">
                <button class="btn align-items-center btn-outline-primary btn-sm btnConnexion" type="submit" name="connexion" value="Connexion">Connexion</button>
                <a class="btn align-items-center btn-outline-primary btn-sm btnConnexion" href="./?page=register">S'inscrire</a>
            </div>
        </div>
    </form>
    <?php
} else {
    echo "<h1>Vous êtes connecté!</h1> </br>";
}

?>