<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 18/01/18
 * Time: 15.01
 */
require_once "utils/lib.hphp";
require_once "utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GUEST);

require_once "../vendor/autoload.php";

$google_client = new Google_Client();
$google_client->setAuthConfig("../client_secret_142180740412-f5mtm2geteu9jn5jgi5b9l9uhva5b40b.apps.googleusercontent.com.json");
$google_client->setRedirectUri("http://localhost:63342/DB_Tirocini/test.php");
$google_client->addScope("https://www.googleapis.com/auth/userinfo.email");
$google_client->addScope("https://www.googleapis.com/auth/userinfo.profile");

$login_url = filter_var($google_client->createAuthUrl(), FILTER_SANITIZE_URL);
?>

<html>
<head>
    <?php include "utils/pages/head.phtml"; ?>

    <style>
        header {
            background: url("asset/school.jpg") center center;
            background-size: cover;
        }
    </style>
</head>
<body>
<header class="hero is-primary is-medium">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                <?php echo SITE_NAME ?>
            </h1>
            <h2>
                <?php echo SITE_SUBTITLE ?>
            </h2>
        </div>
    </div>
</header>
<section>
    <section class="section">
        <div class="container">
            <div class="columns">
                <div class="column">
                    <h1 class="title">Accesso utenti sotto dominio</h1>
                    <h2 class="subtitle">Solo gli utenti autorizzati e sotto dominio <code>itispisa.gov.it</code>
                        potranno effettuare l'accesso</h2>
                    <a
                            class="button is-info is-fullwidth is-large"
                            id="login_google"
                            href="<?= $login_url ?>"
                    >
                        <span class="icon">
                            <i class="fa fa-google" aria-hidden="true"></i>
                        </span>
                            <span>
                            Accedi con Google
                        </span>
                    </a>
                </div>
                <div class="column ">
                    <h1 class="title">Accesso utenze aziendali</h1>
                    <form>
                        <div class="field">
                            <label class="label">
                                Identificativo univoco numerico
                            </label>
                            <div class="control">
                                <input class="input" type="number" placeholder="Mumero" required>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">
                                Parola d'ordine
                            </label>
                            <div class="control">
                                <input class="input" type="password" placeholder="Parola d'ordine">
                            </div>
                        </div>
                        <button class="button is-info is-pulled-right" type="submit">
                            <span>
                                Accedere
                            </span>
                            <span class="icon">
                                <i class="fa fa-sign-in" aria-hidden="true"></i>
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</section>

<script src="index_handlers.js"></script>
</body>
</html>
