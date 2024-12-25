<head>
    <title>Inscription</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="css/structure.css">
    </script>

</head>
<?php if (!((empty($erreurs_formulaire)) && isset($_POST['inscrire_utilisateur']))){


    ?>
    <form method ="post" action="#">
        <fieldset>
            <legend>Informations personnelles</legend>
            <!-- Partie de renseignement login -->
            <div class="align-items-center d-flex flex-row col-4 justify-content-between">
                <!-- Titre -->
                <div class="col-auto">
                    <label class="col-form-label">Nom de Profil :</label>
                </div>
                <!-- Champ de saissie login -->
                <div class="col-auto">
                    <input type="text" name="login" required="required" value="<?php if(isset($_POST['login']))  echo $_POST['login']; ?>"class="form-control <?php echo isset($erreurs_formulaire['login']) ? 'is-invalid' : '' ?>"/>
                    <?php if(isset($erreurs_formulaire['login'])) { ?>
                        <div id="validationServerUsernameFeedback" class="invalid-feedback">
                            <?php echo $erreurs_formulaire['login']?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <!-- Partie de renseignement mot de passe -->
            <div class="align-items-center d-flex flex-row col-4 justify-content-between">
                <!-- Titre -->
                <div class="col-auto">
                    <label class="col-form-label">Mot de passe :</label>
                </div>
                <!-- Champ de saissie mot de passe -->
                <div class="col-auto">
                    <input type="password" name="mdp" required="required" value="<?php if(isset($_POST['mdp']))  echo $_POST['mdp']; ?>" class="form-control">
                </div>
            </div>
            <!-- Partie de renseignement sexe -->
            <div class="g-3 align-items-center d-flex flex-row col-5 justify-content-between">
                <!-- Titre -->
                <div class="col-auto">
                    <label class="col-form-label">Vous êtes (optionnel) :</label>
                </div>
                <!-- Radio pour Femme -->
                <div class="g-3 form-check">
                    <input type="radio" name="sexe" value="femme" <?php if((isset($_POST['sexe']))&&($_POST['sexe'])=='femme') echo 'checked="checked"'; ?> class="form-check-input" >
                    <label class="form-check-label" >une femme</label>
                </div>
                <!-- Radio pour Homme -->
                <div class="g-3 form-check">
                    <input type="radio" name="sexe" value="homme" <?php if((isset($_POST['sexe']))&&($_POST['sexe'])=='homme') echo 'checked="checked"'; ?> class="form-check-input" >
                    <label class="form-check-label" >un homme</label>
                </div>
            </div>
            <!-- Partie de renseignement Nom -->
            <div class="align-items-center d-flex flex-row col-4 justify-content-between">
                <!-- Titre -->
                <div class="col-auto">
                    <label class="col-form-label">Nom (optionnel) :</label>
                </div>
                <!-- Champ de saissie nom-->
                <div class="col-auto">
                    <input type="text" name="nom" value="<?php if(isset($_POST['nom']))  echo $_POST['nom']; ?>" class="form-control <?php echo isset($erreurs_formulaire['nom']) ? 'is-invalid' : '' ?>">
                    <?php if(isset($erreurs_formulaire['nom'])) { ?>
                        <div id="validationServerUsernameFeedback" class="invalid-feedback">
                            <?php echo $erreurs_formulaire['nom'] ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <!-- Partie de renseignement Prenom -->
            <div class="align-items-center d-flex flex-row col-4 justify-content-between">
                <!-- Titre -->
                <div class="col-auto">
                    <label class="col-form-label">Prénom (optionnel) :</label>
                </div>
                <!-- Champ de saissie prenom-->
                <div class="col-auto">
                    <input type="text" name="prenom" value="<?php if(isset($_POST['prenom']))  echo $_POST['prenom']; ?>" class="form-control <?php echo isset($erreurs_formulaire['prenom']) ? 'is-invalid' : '' ?>">
                    <?php if(isset($erreurs_formulaire['prenom'])) { ?>
                        <div id="validationServerUsernameFeedback" class="invalid-feedback">
                            <?php echo $erreurs_formulaire['prenom'] ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <!-- Partie de renseignement Date de Naissance -->
            <div class="align-items-center d-flex flex-row col-4 justify-content-between">
                <!-- Titre -->
                <div class="col-auto">
                    <label class="col-form-label">Date de naissance (optionnel) :</label>
                </div>
                <!-- Champ de saissie date de naissance-->
                <div class="col-auto">
                    <input type="date" placeholder="jj/mm/aaaa" name="naissance" value="<?php if(isset($_POST['naissance']))  echo $_POST['naissance']; ?>"class="form-control <?php echo isset($erreurs_formulaire['naissance']) ? 'is-invalid' : '' ?>">
                    <?php if(isset($erreurs_formulaire['naissance'])) { ?>
                        <div id="validationServerUsernameFeedback" class="invalid-feedback">
                            <?php echo $erreurs_formulaire['naissance'] ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </fieldset>
        <button class="btn btn-primary" type="submit"  name="inscrire_utilisateur" value="Valider">Inscrire</button>
    </form>
    <?php
} else {
    ?>
    <h1>Vous êtes inscrit!</h1> </br>
    <a href='./'>Retourner à l'acceuil</a>
    <?php
}

?>