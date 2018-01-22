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

$login_url = filter_var($google_client->createAuthUrl(), FILTER_SANITIZE_URL);
?>

<html lang="it">
<head>
    <?php include "utils/pages/head.phtml"; ?>

    <script src="https://authedmine.com/lib/captcha.min.js" async></script>
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
                    <h1 class="title">Accesso utenze sotto dominio</h1>
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
                    <?php
                    if(isset($_GET["wrong_domain"]))
                    {
                        ?>
                        <p class="help is-danger">
                            Sta scritto "<em>Solo gli utenti autorizzati e sotto dominio <code>itispisa.gov.it</code>
                                potranno effettuare l'accesso</em>" ma invece avete provato ugualmente.<br>
                            L'utenza è stata disconessa.<br>
                            <em>
                                «[...]del frutto dell'albero che sta in mezzo al giardino Dio ha detto:
                                Non ne dovete mangiare e non lo dovete toccare, altrimenti morirete».
                            </em>
                        </p>
                        <?php
                    }
                    ?>
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

                        <div class="field">
                            <label class="label">
                                Sono umano?<!--Il mio portafogli no-->
                            </label>
                            <div class="coinhive-captcha" data-hashes="1024" data-key="SITE_KEY">
                                <em>
                                    Caricando il "Captcha"...<br>
                                    Se non carica considerare di disattivare AdBlock ovvero concedere il dominio <samp><strong>https://authedmine.com/</strong></samp>.<br>
                                    Questo è necessario per impedire attacchi automatizzati.
                                </em>
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

</body>
</html>
