<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand">Cocktails</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php if (!isset($_GET["page"])) echo "active"; ?>" href="./">Navigation</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isActiveLink('likes') ?>" href="./?page=likes">Recettes
                        <svg xmlns="http://www.w3.org/2000/svg" stroke="black" fill="red" viewBox="0 0.4 16 16"
                             width="16"
                             height="16">
                            <path d="m8 2.1c3.9-3.9 13.6 3 0 12-13.6-9-3.9-15.9 0-12z"/>
                        </svg>
                    </a>
                </li>
            </ul>
            <div class="d-flex">
                <form class="d-flex searchBarForm align-self-end mx-2" action="./" method="get">
                    <?php
                    if (isset($_GET["search"])) {
                        $barreDeRecherche = $_GET["search"];
                    } else {
                        $barreDeRecherche = "";
                    }
                    ?>
                    <input class="form-control form-control-sm" type="search" name="search" placeholder="Recherche"
                           value='<?php echo $barreDeRecherche ?>' aria-label="Search">
                    <button class="btn btn-outline-success btn-sm" type="submit">Rechercher</button>
                </form>
                <div>
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <?php if (isset($_SESSION["isConnected"]) && $_SESSION["isConnected"] == true) {
                            ?>
                            <li class="nav-item">
                                <a class="btn align-items-center btn-outline-primary btn-sm btnConnexion mx-2"
                                   href="./?page=profil">Profil</a>
                            </li>
                            <li class="nav-item">
                                <a class="btn align-items-center btn-outline-primary btn-sm btnConnexion"
                                   href="./?page=sedeconnecter">Se deconnecter</a>
                            </li>

                            <?php
                        } else { ?>
                            <li class="nav-item">
                                <?php include("./includes/components/login.php"); ?>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>