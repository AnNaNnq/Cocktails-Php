<?php
session_start();

// Include the file Donnees.inc.php
include("Donnees.inc.php");
include("includes/functions.php");
include("./includes/validation.php");

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
            crossorigin="anonymous"></script>
    <title>Cocktails</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <link href="style.css" type="text/css" rel="stylesheet"/>
    <script>
        $(function () {
            $(".likebutton").click(function () {
                //Ajout ou retrait de la classe lors du click
                if (!$(this).hasClass("liked")) {
                    $(this).addClass("liked");
                } else {
                    $(this).removeClass("liked");
                }

                /**
                 * On envoie une requête Post contenant dans la variable id_recipe l'id dans Donnees.inc.php de la recette
                 * Ensuite, tout le traitement se passe du côté de PHP
                 * Dans notre cas, on décide d'afficher en console le contenu de la page button_like.php lors du
                 * passage des paramêtres précédents via POST
                 */
                $.post("button_like.php", {id_recipe: $(this).attr("id").substring(5)}).done(function ($data) {
                    console.log($data);
                });
            });
        });
    </script>
</head>

<body>
<?php
include("./includes/components/navbar.php");
?>
<div class="<?php echo (!isset($_GET["search"]) && !isset($_GET["page"])) ? 'pageContainer' : ''; ?>">
    <?php
    if(!isset($_GET["search"]) && !isset($_GET["page"])) {
    ?>
    <div class="grid-hierarchie">
        <div class='card'>
            <div class='card-body'>
                <h1>Hierarchie</h1>
                <?php
                afficherFil();
                afficherSousCategories();
                ?>
            </div>
        </div>
    </div>
    <?php
    }
    ?>
    <div class="container-fluid grid-container <?php echo (!isset($_GET["search"]) && !isset($_GET["page"])) ? '' : 'contentContainer'; ?>">
        <br/>
        <?php
        if (isset($_GET["page"])) {
            if ($_GET["page"] == "login") {
                include("./includes/pages/login.php");
            } else if ($_GET["page"] == "register") {
                include("./includes/pages/inscrire.php");
            } else if ($_GET["page"] == "profil") {
                include("./includes/pages/profil.php");
            } else if ($_GET["page"] == "likes") {
                include("./includes/pages/likes.php");
            } else if ($_GET["page"] == "sedeconnecter") {
                include("./includes/pages/sedeconnecter.php");
            } else if ($_GET["page"] == "details") {
                include("./includes/pages/details.php");
            } else {
                include("./includes/pages/recherche.php");
            }
        } else {
            if (isset($_GET["search"])) {
                include("./includes/pages/recherche.php");
            } else {
                include("./includes/pages/accueil.php");
            }
        }
        ?>
    </div>
</div>
</body>

</html>