<?php
if (file_exists("./donnees.json")) {
    $donnees = json_decode(file_get_contents("donnees.json"), true);
}   
?>
<form method ="post">
    <fieldset>
        <legend>Informations personnelles</legend>
        <!-- Partie de renseignement login -->
        <div class="row g-3 align-items-center">
            <!-- Titre -->
            <div class="col-auto">
                <label class="col-form-label">Nom de Profil :</label>
            </div>
            <!-- Champ de saissie login -->
            <div class="col-auto">
                <?php
                echo $_SESSION["Login"];
                ?>
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
                <input type="password" name="mdp" class="form-control">
            </div>
        </div>
        <!-- Partie de renseignement sexe -->
        <div class="g-3 align-items-center d-flex flex-row col-5 justify-content-between">
            <!-- Titre -->
            <div class="col-auto">
                <label class="col-form-label">Sexe :</label>
            </div>
            <!-- Radio pour Femme -->
            <div class="g-3 form-check">
                <input type="radio" name="sexe" value="femme" <?php echo ($donnees[$_SESSION["Login"]]['sexe']) == "femme" ?  'checked' : '' ;  ?> class="form-check-input" >
                <label class="form-check-label" >une femme</label>
            </div>
            <!-- Radio pour Homme -->
            <div class="g-3 form-check">
                <input type="radio" name="sexe" value="homme" <?php echo ($donnees[$_SESSION["Login"]]['sexe']) == "homme" ?  'checked' : '' ;  ?> class="form-check-input" >
                <label class="form-check-label" >un homme</label>
            </div>
        </div>
        <!-- Partie de renseignement Nom -->
        <div class="align-items-center d-flex flex-row col-4 justify-content-between">
            <!-- Titre -->
            <div class="col-auto">
                <label class="col-form-label">Nom :</label>
            </div>
            <!-- Champ de saissie nom-->
            <div class="col-auto">
                <input type="text" name="nom" value="<?php echo $donnees[$_SESSION["Login"]]['nom']; ?>" class="form-control <?php echo isset($erreurs_formulaire['nom']) ? 'is-invalid' : '' ?>">
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
                <label class="col-form-label">Prénom :</label>
            </div>
            <!-- Champ de saissie prenom-->
            <div class="col-auto">
                <input type="text" name="prenom" value="<?php echo $donnees[$_SESSION["Login"]]['prenom'];?>" class="form-control <?php echo isset($erreurs_formulaire['prenom']) ? 'is-invalid' : '' ?>">
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
                <label class="col-form-label">Date de naissance :</label>
            </div>
            <!-- Champ de saissie date de naissance-->
            <div class="col-auto">
                <input type="date" placeholder="jj/mm/aaaa" name="naissance" value="<?php echo $donnees[$_SESSION["Login"]]['naissance'];?>" class="form-control <?php echo isset($erreurs_formulaire['naissance']) ? 'is-invalid' : '' ?>">
                <?php if(isset($erreurs_formulaire['naissance'])) { ?>
                    <div id="validationServerUsernameFeedback" class="invalid-feedback">
                        <?php echo $erreurs_formulaire['naissance'] ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </fieldset>
    <button class="btn btn-primary" type="submit"  name="modifier_profil" value="Valider la modification">Modifier</button>
</form>