<h1>Recettes likées</h1>
<?php
if (isset($_SESSION["isConnected"]) && $_SESSION["isConnected"]) {
    $donnees = array();
    $login = $_SESSION["Login"];
    if (file_exists("./donnees.json")) {
        $donnees = json_decode(file_get_contents("./donnees.json"), true);
    }
    displayCocktailsWithGivenArrayOfIDs($donnees[$login]["likes"]);
} else {
    displayCocktailsWithGivenArrayOfIDs($_SESSION["likes"]);
}
?>