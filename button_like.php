<?php
session_start();
if (isset($_POST["id_recipe"])) {
    echo "Hello I just recieved: " . $_POST["id_recipe"];
    if (isset($_SESSION["isConnected"])) {
        if (isset($_SESSION["Login"])) {
            $donnees = array();
            $login = $_SESSION["Login"];
            if (file_exists("./donnees.json")) {
                $donnees = json_decode(file_get_contents("./donnees.json"), true);
            }
            if (isset($donnees[$login]["likes"])) {
                if (!in_array($_POST["id_recipe"], $donnees[$login]["likes"])) {
                    $donnees[$login]["likes"][] = $_POST["id_recipe"];
                } else {
                    unset($donnees[$login]["likes"][array_search($_POST["id_recipe"], $donnees[$login]["likes"])]);
                }
            }
            file_put_contents("./donnees.json", json_encode($donnees), JSON_PRETTY_PRINT);
        }
    } else {
        if (isset($_SESSION["likes"])) {
            if (!in_array($_POST["id_recipe"], $_SESSION["likes"])) {
                $_SESSION["likes"][] = $_POST["id_recipe"];
            } else {
                unset($_SESSION["likes"][array_search($_POST["id_recipe"], $_SESSION["likes"])]);
            }
        } else {
            $_SESSION["likes"][] = $_POST["id_recipe"];
        }
    }
} else {
    echo "Hello I just recieved: nothing";
}
