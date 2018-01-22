<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 19/01/18
 * Time: 20.06
 */

require_once "../../utils/lib.hphp";
require_once "../../utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GOOGLE_STUDENT, "./../../");

$google_client->setAccessToken($_SESSION["user"]["token"]);

$oauth2 = new \Google_Service_Oauth2($google_client);
$user = $oauth2->userinfo->get();
?>
<html lang="it">
<head>
    <?php include "../../utils/pages/head.phtml"; ?>
</head>
<body>
<?php include "../common/google_navbar.php"; ?>
<br>
<section class="container">
    <div class="columns">
        <aside class="column is-3 is-fullheight">
            <p class="menu-label">
                Tirocini
            </p>
            <ul class="menu-list">
                <li>
                    <a class="is-active">
                        <span class="icon">
                            <i class="fa fa-play" aria-hidden="true"></i>
                        </span>
                        <span>
                            In corso
                        </span>
                    </a>
                </li>
                <li>
                    <a>
                        <span class="icon">
                            <i class="fa fa-fast-forward" aria-hidden="true"></i>
                        </span>
                        <span>
                            Futuri
                        </span>
                    </a>
                </li>
                <li>
                    <a>
                        <span class="icon">
                            <i class="fa fa-stop" aria-hidden="true"></i>
                        </span>
                        <span>
                            Terminati
                        </span>
                    </a>
                </li>
            </ul>
        </aside>
        <div class="column">
            <?php
            for($i = 0; $i < 5; $i++):
            ?>
                <article class="card">
                    <header class="card-header">
                        <h1 class="card-header-title">
                            Stage a Black Mesa corp.
                        </h1>
                    </header>
                    <div class="card-content">
                        <div class="content">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus nec iaculis mauris.
                            <a href="#">@bulmaio</a>. <a href="#">#css</a> <a href="#">#responsive</a>
                            <br>
                            <time datetime="2016-01-01">11:09 PM - 1 Jan 2016</time>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <a href="#" class="card-footer-item">
                            <span class="icon">
                                <i class="fa fa-pencil-square" aria-hidden="true"></i>
                            </span>
                            <span>
                                Scrivi un resoconto
                            </span>
                        </a>
                    </footer>
                </article>
            <br>
            <?php
            endfor;
            ?>
        </div>
    </div>
</section>
</body>
</html>
