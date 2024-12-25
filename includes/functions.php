<?php
$scores = array();
include("variables_globales.php");
$fil = isset($_GET["fil"]) ? explode(",", $_GET["fil"]) : ["Aliment"]; //Enregistrement du contenu du fil passé dans $_GET dans la variable fil
$categorie_actuelle = $fil[sizeof($fil) - 1]; //Enregistrement de la catégorie actuelle

//Search

// Generer l'affichage de la recherche
function displayCocktailsForActualSearch()
{
    displayCocktailsWithGivenArrayOfIDs(getRecipesToDisplayForSearch());
}

// Fonction de gestion de la barre de recherche
function getRecipesToDisplayForSearch()
{
    global $Recettes, $Hierarchie, $resultatRecherche, $scores;

    // Gestion de la barre de recherche

    if (isset($_GET["search"])) { //"Jus de Pamplemousse" "Jus de Citron" -"Jus de Kiwi" -Tomates +Abricots Salade
        $search = " " . $_GET["search"] . " ";

        //Vérification du nombre de guillemets, si impair affichage d'une erreur
        if (substr_count($search, '"') % 2 != 0) {
            ?>
            <p>Problème de syntaxe dans votre requête : nombre impair de double-quotes</p>
            <?php
        } else {
            //Initialisation des variables
            $resultatRecherche = array();
            $nonReconnus = array();
            //Dédoublement des espaces afin de faciliter le traitement
            $search = str_replace(" ", "  ", $search);

            //Traitement et enregistrement des mots entre guillemets dans le tableau $resultatRecherche
            preg_match_all('# ([+-]?)"([^"]+)" #', $search, $quotesMatches);
            foreach ($quotesMatches[0] as $keyMatch => $valueMatch) {
                $resultatRecherche[($quotesMatches[1][$keyMatch] == '-') ? '-' : '+'][] = str_replace("  ", " ", $quotesMatches[2][$keyMatch]);
                $search = str_replace($valueMatch, " ", $search);
            }

            //Vérifie qu'il n'y ait pas de symbole + ou - dans le mot apres lui avoir retiré la premiere lettre, dans le cas contraire ajout aux mots non reconnus
            foreach (explode(" ", $search) as $word) {
                if ($word != "") {
                    if (is_bool(strpos(substr($word, 1), '+')) || is_bool(strpos(substr($word, 1), '-'))) {
                        if (in_array($word[0], array("+", "-"))) {
                            $resultatRecherche[$word[0]][] = str_replace("  ", " ", substr($word, 1));
                        } else {
                            $resultatRecherche['+'][] = str_replace("  ", " ", $word);
                        }
                    } else {
                        $nonReconnus[] = $word;
                    }
                }
            }

            //Vérification de la présence des éléments recherchés dans la hierarchie ainsi que de la présence d'éléments non reconnus dans l'array $resultatRecherche
            foreach ($resultatRecherche as $keySymbol => $symbol) {
                foreach ($symbol as $keyWord => $word) {
                    if (strpos($word, "+") !== false || strpos($word, "-") !== false || strpos($word, '"') !== false) {
                        $nonReconnus[] = $word;
                        if (isset($resultatRecherche[$keySymbol][$keyWord])) {
                            unset($resultatRecherche[$keySymbol][$keyWord]);
                        }
                    } else {
                        if (!isset($Hierarchie[$word])) {
                            $nonReconnus[] = $word;
                            if (isset($resultatRecherche[$keySymbol][$keyWord])) {
                                unset($resultatRecherche[$keySymbol][$keyWord]);
                            }
                        }
                    }
                }
            }

            // Gestion de l'affichage des erreurs et messages d'information
            if (isset($resultatRecherche['+']) || isset($resultatRecherche['-'])) {
                ?>
                <div class="alert alert-success" role="alert">
                    <?php
                    if (isset($resultatRecherche['+'])) {
                        ?><p>Liste des aliments souhaités
                        : <?php echo implode(", ", $resultatRecherche['+']); ?></p><?php
                    }
                    if (isset($resultatRecherche['-'])) {
                        ?><p class="my-0">Liste des aliments non souhaités
                        : <?php echo implode(", ", $resultatRecherche['-']); ?></p><?php
                    }
                    ?>
                </div>
                <?php
            }
            if (isset($nonReconnus) && sizeof($nonReconnus) > 0) {
                ?>
                <div class="alert alert-danger" role="alert">
                    Éléments non reconnus dans la requête : <?php echo implode(", ", $nonReconnus); ?>
                </div>
                <?php
            }

            // Gestion de l'affichage des résultats

            $filtered_recipes = $Recettes;
            $recipesIds = array();
            $ingredientsPlus = array();
            $ingredientsMinus = array();
            if (isset($resultatRecherche["-"])) {
                foreach ($resultatRecherche["-"] as $ingredient) {
                    $ingredientsMinus = array_merge(getIngredientsList($ingredient), $ingredientsMinus);
                }
            }
            if (isset($resultatRecherche["+"])) {
                $ingredientsPlus = $resultatRecherche["+"];
            }
            foreach ($filtered_recipes as $recipeKey => $recette) {
                $countIngredientsPlus = 0;
                $hasIngredientMinus = false;
                foreach ($ingredientsPlus as $ingredient) {
                    if (in_array($ingredient, $recette["index"])) {
                        $countIngredientsPlus++;
                    }
                }
                foreach ($ingredientsMinus as $ingredient) {
                    if (in_array($ingredient, $recette["index"])) {
                        $hasIngredientMinus = true;
                    }
                }

                if ($hasIngredientMinus || $countIngredientsPlus == 0) {
                    unset($filtered_recipes[$recipeKey]);
                } else {
                    $filtered_recipes[$recipeKey]["score"] = round(($countIngredientsPlus / count($resultatRecherche["+"])) * 100);
                }
            }
            uasort($filtered_recipes, function ($a, $b) {
                return $b['score'] - $a['score'];
            });

            foreach ($filtered_recipes as $recipeKey => $recipeValue) {
                $recipesIds[] = $recipeKey;
                $scores[$recipeKey] = $recipeValue["score"];
            }
            return $recipesIds;
        }
    }
    return array();
}

// Gestion du Fil d'Arianne

// Fonction qui permet de récupérer la liste des parents d'un élément, et de l'élément lui-même, et de l'afficher sous forme de fil d'Arianne
function afficherFil()
{
    global $fil;
    foreach ($fil as $category_id => $super_categorie) { //Affichage du fil
        if ($category_id != 0) { ?> / <?php
        } ?><a
        href="./?fil=<?php echo implode(",", array_slice($fil, 0, array_search($super_categorie, $fil) + 1)) ?>"><?php echo $super_categorie; ?></a>
        <?php
    }
}

// Fonction qui permet d'afficher la liste des sous-catégories d'une catégorie
function afficherSousCategories()
{
    global $Hierarchie, $categorie_actuelle, $sous_categorie, $fil;
    if (isset($Hierarchie[$categorie_actuelle]["sous-categorie"])) { //Cas où il y a au moins une sous-catégorie

        ?>
        <ul><?php
        foreach ($Hierarchie[$categorie_actuelle]["sous-categorie"] as $sous_categorie) { // Pour chaque sous-catégorie, l'ajouter a l'affichage des sous-catégories

            ?>
            <li>
                <a href="./?fil=<?php echo(implode(",", array_merge($fil, [$sous_categorie]))) ?>"><?php echo $sous_categorie; ?></a>
            </li>
            <?php
        }
        ?></ul><?php
    } else { //Cas où il n'y a pas de sous-catégories

        ?>
        <h2>Pas de sous catégories</h2>
        <?php
    }
}

//Fonction qui renvoie un tableau contenant l'ingrédient lui-même et ses sous-catégories
function getIngredientsList(string $ingredient)
{
    global $Hierarchie;
    if (!isset($Hierarchie[$ingredient])) {
        return array();
    }
    $ingredients = array(
        $ingredient
    );
    if (isset($Hierarchie[$ingredient]["sous-categorie"])) {
        foreach ($Hierarchie[$ingredient]["sous-categorie"] as $sous_categorie) {
            $ingredients = array_unique(array_merge($ingredients, getIngredientsList($sous_categorie)));
        }
    }
    return $ingredients;
}

// Fonction qui renvoie un tableau contenant tout les identifiants des recettes qui contiennent l'ingrédient ou un ingrédient des sous-catégories
function getRecipesToDisplay($category_to_search)
{
    global $Recettes;
    $filtered_recipes = $Recettes;
    $recipesIds = array();
    $ingredients = getIngredientsList($category_to_search);
    // On filtre les recettes
    foreach ($filtered_recipes as $recipeKey => $recette) {
        $containsIngredient = false;
        foreach ($ingredients as $ingredient) {
            if (in_array($ingredient, $recette["index"])) {
                $containsIngredient = true;
                break;
            }
        }
        if (!$containsIngredient) unset($filtered_recipes[$recipeKey]);
    }
    foreach ($filtered_recipes as $recipeKey => $recette) {
        $recipesIds[] = $recipeKey;
    }
    return $recipesIds;
}

// Fonction qui permet de savoir si le nom de la page donné en argument est le nom de la page actuelle
function isActiveLink($linkName)
{
    if (isset($_GET["page"])) {
        return ($_GET["page"] == $linkName) ? "active" : "";
    }
    return "";
}

// Fonction permettant d'afficher le détail d'une recette
function displayFullRecipe($Recette, $recetteId)
{
    ?>
    <div class="d-flex flex-row">
        <div id="like-<?php echo $recetteId ?>"
             class="likebutton <?php echo isRecipeLiked($recetteId) ? 'liked' : '' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" stroke="black" fill="none" viewBox="0 0 16 16" width="30"
                 height="30">
                <path d="m8 2.1c3.9-3.9 13.6 3 0 12-13.6-9-3.9-15.9 0-12z"/>
            </svg>

        </div>
        <h2 class="mx-2"> <?php echo $Recette["titre"] ?></h2>
    </div>
    <img style="max-height: 200px;" class="img-fluid m-4 d-block"
         src="<?php echo getProductImagePath($Recette["titre"]) ?>"
         alt="Photo de verre de <?php echo $Recette["titre"] ?>">
    <h3>Ingrédients :</h3>
    <ul>
        <?php
        foreach (explode("|", $Recette["ingredients"]) as $ingredient) {
            ?>
            <li><?php echo $ingredient ?></li>
            <?php
        }
        ?>
    </ul>
    <h3>Préparation :</h3>
    <ol>
        <?php
        foreach (explode(".", $Recette["preparation"]) as $instruction) {
            if ($instruction != "") {

                ?>
                <li><?php echo $instruction ?></li>
                <?php
            }
        }
        ?>
    </ol>
    <?php
}

// Fonction qui renvoie un boolean indiquant si la recette est likée ou non
function isRecipeLiked($identifiantRecette)
{
    if (isset($_SESSION["isConnected"])) {
        $donnees = array();
        if (isset($_SESSION["Login"])) {
            $login = $_SESSION["Login"];
            if (file_exists("./donnees.json")) {
                $donnees = json_decode(file_get_contents("./donnees.json"), true);
            }
            if (isset($donnees[$login]["likes"])) {
                return in_array($identifiantRecette, $donnees[$login]["likes"]);
            }
            return false;
        }
    } else {
        if (isset($_SESSION["likes"])) {
            return in_array($identifiantRecette, $_SESSION["likes"]);
        } else {
            return false;
        }
    }
}

// Fonction qui permet d'afficher une carte de recette
function displayRecipe($identifiantRecette, $title, $ingredients, $score = -1)
{
    ?>
    <div class='col-4 card'>
        <div class='card-body'>
            <div style="display:flex; justify-content: space-between;">
                <h2><a style="color:black; text-decoration: none;"
                       href="./?page=details&id=<?php echo $identifiantRecette ?>"><?php echo $title ?></a></h2>
                <div id="like-<?php echo $identifiantRecette ?>"
                     class="likebutton <?php echo isRecipeLiked($identifiantRecette) ? 'liked' : '' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" stroke="black" fill="none" viewBox="0 0 16 16" width="30"
                         height="30">
                        <path d="m8 2.1c3.9-3.9 13.6 3 0 12-13.6-9-3.9-15.9 0-12z"/>
                    </svg>
                </div>
            </div>
            <img style="max-height: 200px;" class="img-fluid mx-auto m-4 d-block"
                 src="<?php echo getProductImagePath($title) ?>" alt="Photo de verre de <?php echo $title ?>">
            <ul>
                <?php
                foreach ($ingredients as $Ingredient) {
                    ?>
                    <li><?php echo $Ingredient ?></li>
                    <?php
                }
                ?>
            </ul>
        </div>
        <?php
        if ($score != -1) {
            ?>
            <div class="card-footer bg-transparent">
                <p class="text-secondary my-0">Cette recette respecte votre demande à :</p>
                <div class="progress">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $score ?>%;"
                         aria-valuenow="<?php echo $score ?>" aria-valuemin="0"
                         aria-valuemax="100"><?php echo $score ?>
                        %
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
}

// Fonction qui permet de récupérer le chemin vers l'image d'une recette
function getProductImagePath($title)
{
    $defaultFilePath = "./Photos/" . ucfirst(strtolower(normalizeChars($title))) . ".jpg";
    return file_exists($defaultFilePath) ? $defaultFilePath : "./Photos/cocktail.png";
}

// Fonction qui permet d'afficher tous les cocktails contenus dans un tableau d'identifiants de recettes
function displayCocktailsWithGivenArrayOfIDs($arrayOfIDs)
{
    global $scores;
    ?>
    <?php
    global $Recettes;
    $counter = 0;
    if (isset($arrayOfIDs)) {
        foreach ($arrayOfIDs as $id) {
            if ($counter % 3 == 0) {
                ?>
                <div class="row">
                <?php
            }
            if (isset($scores[$id]) && $scores[$id] != -1) {
                displayRecipe($id, $Recettes[$id]["titre"], $Recettes[$id]["index"], $scores[$id]);
            } else {
                displayRecipe($id, $Recettes[$id]["titre"], $Recettes[$id]["index"]);
            }
            if ($counter % 3 == 2) {
                ?>
                </div>
                <?php
            }
            $counter++;
        }
    }
    $scores = array();
}

// Fonction qui permet d'afficher tous les cocktails pour une catégorie donnée, ainsi que ses souc-catégories
function displayCocktailsWithGivenCategory($categorie_choisie)
{
    displayCocktailsWithGivenArrayOfIDs(getRecipesToDisplay($categorie_choisie));
}

?>

    <!-- Anton -->


<?php

function seConnecter()
{
    //Tableau pour contenir les erreurs
    global $erreurs_formulaire;
    //Vérifie si le fichier existe
    if (file_exists("./donnees.json")) {
        $donnees = json_decode(file_get_contents("donnees.json"), true);
    } else {
        $donnees = array();
    }

    $recherche_login = $_POST['login'];
    $mdp_a_verifier = $_POST["mdp"];
    //Vérifie si le login existe
    if (isset($donnees[$recherche_login]["password"])) {
        $donnees_mdp = $donnees[$recherche_login]["password"];
        //Vérifie si le mot de passe est bon, sinon message d'erreur
        if (password_verify($mdp_a_verifier, $donnees_mdp)) {
            $_SESSION["isConnected"] = true;
            $_SESSION["Login"] = $_POST['login'];
            if (isset($_SESSION["likes"])) {
                $donnees[$_SESSION["Login"]]["likes"] = array_unique(array_merge($donnees[$_SESSION["Login"]]["likes"], $_SESSION["likes"]));
                $_SESSION["likes"] = array();
                file_put_contents("./donnees.json", json_encode($donnees, JSON_PRETTY_PRINT));
            } else {
                $erreurs_formulaire["mdp"] = "Mot de passe invalide";
            }
        } else {
            $erreurs_formulaire["login"] = "Ce Login n'existe pas";
        }
    } else {
        $erreurs_formulaire["login"] = "Ce Login n'existe pas";
    }
}

function ajouterUser()
{
    //Tableau pour contenir les erreurs
    global $erreurs_formulaire;

    //Vérifie si le login existe et qu'il n'existe pas, sinon erreur
    if
    (verifierSiLoginExiste($_POST['login']) == true) {
        $erreurs_formulaire["login"] = "Ce Login existe déjà";
    } elseif
    (verifierLogin($_POST['login']) == false) {
        $erreurs_formulaire["login"] = "Le Login peut être composé de lettres minuscules et/ou de lettres MAJUSCULES, ainsi que les caractères
        « - », « » (espace) et « ’ » ";
    } elseif
    (verifierLogin($_POST['login']) == true && verifierSiLoginExiste($_POST['login']) == false) {
        if (file_exists("./donnees.json")) {
            $donnees = json_decode(file_get_contents("./donnees.json"), true);
        } else {
            $donnees = array();
        }

        //variable qui s'incrémente si le format de Nom, Prenom et Naissance sont valides ou vides
        $valider = 0;
        //variable boolean qui vérifie si le mot de passe est vide ou non
        $mdp_non_valide = true;

        //Enregistrer et Hashage du mot de passe
        if (isset($_POST["mdp"]) && $_POST["mdp"] != '') {
            $donnees[$_POST['login']]['password'] = password_hash($_POST["mdp"], PASSWORD_DEFAULT);
        } else {
            $mdp_non_valide = false;
        }

        //Enregistrer sexe
        if (isset($_POST["sexe"])) {
            if ($_POST["sexe"] == 'homme' || $_POST["sexe"] == 'femme') {
                $donnees[$_POST['login']]['sexe'] = $_POST["sexe"];
            }
        } else {
            $donnees[$_POST['login']]['sexe'] = "";
        }

        //Enregistrer Nom
        if (isset($_POST["nom"])) {
            if (verifierNomPrenom($_POST["nom"]) == true || $_POST["nom"] == "") {
                $donnees[$_POST['login']]['nom'] = $_POST["nom"];
                $valider++;
            } elseif (verifierNomPrenom($_POST["nom"]) == false) {
                $erreurs_formulaire["nom"] = "Mauvais format du nom";
            }
        }

        //Enregistrer Prenom
        if (isset($_POST["prenom"])) {
            if (verifierNomPrenom($_POST["prenom"]) == true || $_POST["prenom"] == "") {
                $donnees[$_POST['login']]['prenom'] = $_POST["prenom"];
                $valider++;
            } elseif (verifierNomPrenom($_POST["prenom"]) == false) {
                $erreurs_formulaire["prenom"] = "Mauvais format du prenom";
            }
        }

        //Enregistrer Date de Naissance
        if (isset($_POST["naissance"])) {
            if ($_POST["naissance"] == "") {
                $donnees[$_POST['login']]['naissance'] = $_POST["naissance"];
                $valider++;
            } else {
                $date = adapterFormatDate($_POST["naissance"]);
                if (verifierDatedeNaissance($date) == true) {
                    $donnees[$_POST['login']]['naissance'] = $date;

                } elseif (verifierDatedeNaissance($date) == false) {
                    if (verifierDateEstDansLeFutur($date) == true) {
                        $erreurs_formulaire["naissance"] = "Vous ne pouvez pas avoir une date dans le futur";
                    } else {
                        $erreurs_formulaire["naissance"] = "Vous êtes mineurs";
                    }
                }
            }

        }

        //Intitialiser les likes
        $donnees[$_POST['login']]['likes'] = [];

        //si le format de Nom, Prenom et Naissance sont validées ou vides et le mot de passe est n'est pas vides, on enrengistre dans la base de données
        if ($valider == 3 && $mdp_non_valide == true) {
            file_put_contents("donnees.json", json_encode($donnees, JSON_PRETTY_PRINT));
        }
    }
}

//function qui prend une date est le change sous le format américain
function adapterFormatDate($une_date)
{
    $date_format_us = str_replace('/', '-', $une_date);
    $date_formater = date("Y-m-d", strtotime($date_format_us));
    return $date_formater;
}

function changerProfil()
{
    global $erreurs_formulaire;
    //Vérifie si le fichier existe
    if (file_exists("./donnees.json")) {
        $donnees = json_decode(file_get_contents("./donnees.json"), true);

        //variable qui s'incrémente si le format de Nom, Prenom et Naissance sont valides
        $valider = 0;

        //Enregistrer et Hashage du mot de passe
        if (isset($_POST["mdp"]) && $_POST["mdp"] != "") {
            $donnees[$_SESSION["Login"]]['password'] = password_hash($_POST["mdp"], PASSWORD_DEFAULT);
        }

        //Enregistrer sexe
        if (isset($_POST["sexe"]) && $_POST["sexe"] != "") {
            if ($_POST["sexe"] == 'homme' || $_POST["sexe"] == 'femme') {
                $donnees[$_SESSION["Login"]]['sexe'] = $_POST["sexe"];
            }
        }

        //Enregistrer Nom
        if (isset($_POST["nom"]) && $_POST["nom"] != "") {
            if (verifierNomPrenom($_POST["nom"]) == true) {
                $donnees[$_SESSION["Login"]]['nom'] = $_POST["nom"];
                $valider++;
            } elseif (verifierNomPrenom($_POST["nom"]) == false) {
                $erreurs_formulaire["nom"] = "Mauvais format du nom";
            }
        } else {
            $donnees[$_SESSION["Login"]]['nom'] = "";
        }

        //Enregistrer Prenom
        if (isset($_POST["prenom"]) && $_POST["prenom"] != "") {
            if (verifierNomPrenom($_POST["prenom"]) == true) {
                $donnees[$_SESSION["Login"]]['prenom'] = $_POST["prenom"];
                $valider++;
            } elseif (verifierNomPrenom($_POST["prenom"]) == false) {
                $erreurs_formulaire["prenom"] = "Mauvais format du prenom";
            }
        } else {
            $donnees[$_SESSION["Login"]]['prenom'] = "";
        }

        //Enregistrer Date de Naissance
        if (isset($_POST["naissance"]) && $_POST["naissance"] != "") {
            $date = adapterFormatDate($_POST["naissance"]);
            if (verifierDatedeNaissance($date) == true) {
                $donnees[$_SESSION["Login"]]['naissance'] = $date;
                $valider++;
            } elseif (verifierDatedeNaissance($date) == false) {
                if (verifierDateEstDansLeFutur($date) == true) {
                    $erreurs_formulaire["naissance"] = "Vous ne pouvez pas avoir une date dans le futur";
                } else {
                    $erreurs_formulaire["naissance"] = "Vous êtes mineurs";
                }
            }
        } else {
            $donnees[$_SESSION["Login"]]['naissance'] = "";
        }

        //si le format de Nom, Prenom et Naissance sont validées, on enrengistre dans la base de données
        if ($valider <= 3) {
            file_put_contents("donnees.json", json_encode($donnees, JSON_PRETTY_PRINT));
        }
    }
}

function verifierLogin($un_login)
{
    $pattern_login = "/^[a-zA-Z0-9]+$/";
    $verifie = preg_match($pattern_login, $un_login);
    if ($verifie == 1) {
        return true;
    } else {
        return false;
    }
}

function verifierNomPrenom($un_nom_prenom)
{
    $pattern_nom_prenom = "/^[a-zA-Z '-]+$/";
    $verifie = preg_match($pattern_nom_prenom, $un_nom_prenom);
    if ($verifie == 1) {
        return true;
    } else {
        return false;
    }
}

/*
 * function qui vérifie si l'utilisateur est majeur ou mineur.
 * return true, l'utilisateur est majeure
 * return false, l'utilisateur est mineur
*/
function verifierDatedeNaissance($une_date)
{
    $date_naissance_trier = explode("-", $une_date);
    // verifier que la date saissi soit valide
    if (checkdate($date_naissance_trier[1], $date_naissance_trier[2], $date_naissance_trier[0])) {
        // initialiser la date d'ajourd'hui
        $date_courrant = date("Y-m-d");
        $date_courant_trier = explode("-", $date_courrant);
        // algorithme qui vérifie si l'utilisateur est majeur
        $annee = $date_courant_trier[0] - $date_naissance_trier[0];
        if ($annee > 18) {
            return true;
        } else if ($annee == 18) {
            //comparez le mois
            if ($date_naissance_trier[1] >= $date_courant_trier[1]) {
                //comparez la date
                if ($date_naissance_trier[2] >= $date_courant_trier[2]) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    } else {
        return false;
    }
}

/*
 * function qui vérifie si la date donné est dans le futur.
 * return true, la date est dans le futur
 * return false, la date n'est pas dans le futur
*/
function verifierDateEstDansLeFutur($une_date)
{
    $date_naissance_trier = explode("-", $une_date);
    // verifier que la date saissi soit valide
    if (checkdate($date_naissance_trier[1], $date_naissance_trier[2], $date_naissance_trier[0])) {
        // initialiser la date d'ajourd'hui
        $date_courrant = date("Y-m-d");
        $date_courant_trier = explode("-", $date_courrant);
        $annee = $date_naissance_trier[0] - $date_courant_trier[0];
        if ($annee > 0) {
            return true;
        } else if ($annee == 0) {
            //comparez le mois
            if ($date_naissance_trier[1] <= $date_courant_trier[1]) {
                //comparez la date
                if ($date_naissance_trier[2] <= $date_courant_trier[2]) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        } else {
            return false;
        }
    } else {
        return true;
    }
}

function verifierSiLoginExiste($un_login)
{
    if (file_exists("./donnees.json")) {
        $donnees = json_decode(file_get_contents("./donnees.json"), true);
    } else {
        $donnees = array();
    }
    $recherche_login = $un_login;
    if (isset($donnees[$recherche_login])) {
        return true;
    } else {
        return false;
    }
}

// Fonction qui permet d'enlever tous les accents et de transformer les caractères spéciaux en caractères normaux
function normalizeChars($s)
{
    $replace = array(
        'ъ' => '-',
        'Ь' => '-',
        'Ъ' => '-',
        'ь' => '-',
        'Ă' => 'A',
        'Ą' => 'A',
        'À' => 'A',
        'Ã' => 'A',
        'Á' => 'A',
        'Æ' => 'A',
        'Â' => 'A',
        'Å' => 'A',
        'Ä' => 'Ae',
        'Þ' => 'B',
        'Ć' => 'C',
        'ץ' => 'C',
        'Ç' => 'C',
        'È' => 'E',
        'Ę' => 'E',
        'É' => 'E',
        'Ë' => 'E',
        'Ê' => 'E',
        'Ğ' => 'G',
        'İ' => 'I',
        'Ï' => 'I',
        'Î' => 'I',
        'Í' => 'I',
        'Ì' => 'I',
        'Ł' => 'L',
        'Ñ' => 'N',
        'Ń' => 'N',
        'Ø' => 'O',
        'Ó' => 'O',
        'Ò' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ö' => 'Oe',
        'Ş' => 'S',
        'Ś' => 'S',
        'Ș' => 'S',
        'Š' => 'S',
        'Ț' => 'T',
        'Ù' => 'U',
        'Û' => 'U',
        'Ú' => 'U',
        'Ü' => 'Ue',
        'Ý' => 'Y',
        'Ź' => 'Z',
        'Ž' => 'Z',
        'Ż' => 'Z',
        'â' => 'a',
        'ǎ' => 'a',
        'ą' => 'a',
        'á' => 'a',
        'ă' => 'a',
        'ã' => 'a',
        'Ǎ' => 'a',
        'а' => 'a',
        'А' => 'a',
        'å' => 'a',
        'à' => 'a',
        'א' => 'a',
        'Ǻ' => 'a',
        'Ā' => 'a',
        'ǻ' => 'a',
        'ā' => 'a',
        'ä' => 'ae',
        'æ' => 'ae',
        'Ǽ' => 'ae',
        'ǽ' => 'ae',
        'б' => 'b',
        'ב' => 'b',
        'Б' => 'b',
        'þ' => 'b',
        'ĉ' => 'c',
        'Ĉ' => 'c',
        'Ċ' => 'c',
        'ć' => 'c',
        'ç' => 'c',
        'ц' => 'c',
        'צ' => 'c',
        'ċ' => 'c',
        'Ц' => 'c',
        'Č' => 'c',
        'č' => 'c',
        'Ч' => 'ch',
        'ч' => 'ch',
        'ד' => 'd',
        'ď' => 'd',
        'Đ' => 'd',
        'Ď' => 'd',
        'đ' => 'd',
        'д' => 'd',
        'Д' => 'D',
        'ð' => 'd',
        'є' => 'e',
        'ע' => 'e',
        'е' => 'e',
        'Е' => 'e',
        'Ə' => 'e',
        'ę' => 'e',
        'ĕ' => 'e',
        'ē' => 'e',
        'Ē' => 'e',
        'Ė' => 'e',
        'ė' => 'e',
        'ě' => 'e',
        'Ě' => 'e',
        'Є' => 'e',
        'Ĕ' => 'e',
        'ê' => 'e',
        'ə' => 'e',
        'è' => 'e',
        'ë' => 'e',
        'é' => 'e',
        'ф' => 'f',
        'ƒ' => 'f',
        'Ф' => 'f',
        'ġ' => 'g',
        'Ģ' => 'g',
        'Ġ' => 'g',
        'Ĝ' => 'g',
        'Г' => 'g',
        'г' => 'g',
        'ĝ' => 'g',
        'ğ' => 'g',
        'ג' => 'g',
        'Ґ' => 'g',
        'ґ' => 'g',
        'ģ' => 'g',
        'ח' => 'h',
        'ħ' => 'h',
        'Х' => 'h',
        'Ħ' => 'h',
        'Ĥ' => 'h',
        'ĥ' => 'h',
        'х' => 'h',
        'ה' => 'h',
        'î' => 'i',
        'ï' => 'i',
        'í' => 'i',
        'ì' => 'i',
        'į' => 'i',
        'ĭ' => 'i',
        'ı' => 'i',
        'Ĭ' => 'i',
        'И' => 'i',
        'ĩ' => 'i',
        'ǐ' => 'i',
        'Ĩ' => 'i',
        'Ǐ' => 'i',
        'и' => 'i',
        'Į' => 'i',
        'י' => 'i',
        'Ї' => 'i',
        'Ī' => 'i',
        'І' => 'i',
        'ї' => 'i',
        'і' => 'i',
        'ī' => 'i',
        'ĳ' => 'ij',
        'Ĳ' => 'ij',
        'й' => 'j',
        'Й' => 'j',
        'Ĵ' => 'j',
        'ĵ' => 'j',
        'я' => 'ja',
        'Я' => 'ja',
        'Э' => 'je',
        'э' => 'je',
        'ё' => 'jo',
        'Ё' => 'jo',
        'ю' => 'ju',
        'Ю' => 'ju',
        'ĸ' => 'k',
        'כ' => 'k',
        'Ķ' => 'k',
        'К' => 'k',
        'к' => 'k',
        'ķ' => 'k',
        'ך' => 'k',
        'Ŀ' => 'l',
        'ŀ' => 'l',
        'Л' => 'l',
        'ł' => 'l',
        'ļ' => 'l',
        'ĺ' => 'l',
        'Ĺ' => 'l',
        'Ļ' => 'l',
        'л' => 'l',
        'Ľ' => 'l',
        'ľ' => 'l',
        'ל' => 'l',
        'מ' => 'm',
        'М' => 'm',
        'ם' => 'm',
        'м' => 'm',
        'ñ' => 'n',
        'н' => 'n',
        'Ņ' => 'n',
        'ן' => 'n',
        'ŋ' => 'n',
        'נ' => 'n',
        'Н' => 'n',
        'ń' => 'n',
        'Ŋ' => 'n',
        'ņ' => 'n',
        'ŉ' => 'n',
        'Ň' => 'n',
        'ň' => 'n',
        'о' => 'o',
        'О' => 'o',
        'ő' => 'o',
        'õ' => 'o',
        'ô' => 'o',
        'Ő' => 'o',
        'ŏ' => 'o',
        'Ŏ' => 'o',
        'Ō' => 'o',
        'ō' => 'o',
        'ø' => 'o',
        'ǿ' => 'o',
        'ǒ' => 'o',
        'ò' => 'o',
        'Ǿ' => 'o',
        'Ǒ' => 'o',
        'ơ' => 'o',
        'ó' => 'o',
        'Ơ' => 'o',
        'œ' => 'oe',
        'Œ' => 'oe',
        'ö' => 'oe',
        'פ' => 'p',
        'ף' => 'p',
        'п' => 'p',
        'П' => 'p',
        'ק' => 'q',
        'ŕ' => 'r',
        'ř' => 'r',
        'Ř' => 'r',
        'ŗ' => 'r',
        'Ŗ' => 'r',
        'ר' => 'r',
        'Ŕ' => 'r',
        'Р' => 'r',
        'р' => 'r',
        'ș' => 's',
        'с' => 's',
        'Ŝ' => 's',
        'š' => 's',
        'ś' => 's',
        'ס' => 's',
        'ş' => 's',
        'С' => 's',
        'ŝ' => 's',
        'Щ' => 'sch',
        'щ' => 'sch',
        'ш' => 'sh',
        'Ш' => 'sh',
        'ß' => 'ss',
        'т' => 't',
        'ט' => 't',
        'ŧ' => 't',
        'ת' => 't',
        'ť' => 't',
        'ţ' => 't',
        'Ţ' => 't',
        'Т' => 't',
        'ț' => 't',
        'Ŧ' => 't',
        'Ť' => 't',
        '™' => 'tm',
        'ū' => 'u',
        'у' => 'u',
        'Ũ' => 'u',
        'ũ' => 'u',
        'Ư' => 'u',
        'ư' => 'u',
        'Ū' => 'u',
        'Ǔ' => 'u',
        'ų' => 'u',
        'Ų' => 'u',
        'ŭ' => 'u',
        'Ŭ' => 'u',
        'Ů' => 'u',
        'ů' => 'u',
        'ű' => 'u',
        'Ű' => 'u',
        'Ǖ' => 'u',
        'ǔ' => 'u',
        'Ǜ' => 'u',
        'ù' => 'u',
        'ú' => 'u',
        'û' => 'u',
        'У' => 'u',
        'ǚ' => 'u',
        'ǜ' => 'u',
        'Ǚ' => 'u',
        'Ǘ' => 'u',
        'ǖ' => 'u',
        'ǘ' => 'u',
        'ü' => 'ue',
        'в' => 'v',
        'ו' => 'v',
        'В' => 'v',
        'ש' => 'w',
        'ŵ' => 'w',
        'Ŵ' => 'w',
        'ы' => 'y',
        'ŷ' => 'y',
        'ý' => 'y',
        'ÿ' => 'y',
        'Ÿ' => 'y',
        'Ŷ' => 'y',
        'Ы' => 'y',
        'ž' => 'z',
        'З' => 'z',
        'з' => 'z',
        'ź' => 'z',
        'ז' => 'z',
        'ż' => 'z',
        'ſ' => 'z',
        'Ж' => 'zh',
        'ж' => 'zh',
        " " => "_"
    );
    return strtr($s, $replace);
}

?>