<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 19/01/18
 * Time: 18.56
 */
require_once "utils/const.hphp";
$page = "Pagina d'errore";

if (isset($_GET["error"]))
    $errore = json_decode(urldecode($_GET["error"]), true);
else if(isset($_GET["session_mode"]))
{
    session_start();
    $errore = $_SESSION["last_error"];
}
else
    $errore = array(
        "name" => "Nessun argomento fornito :P",
        "code" => -1,
        "what" => "$ ping informaticapisa.jimbdo.com
PING jimbdo.com (67.227.226.241) 56(84) bytes of data.
64 bytes from 67.227.226.241: icmp_seq=1 ttl=43 time=204 ms
64 bytes from 67.227.226.241: icmp_seq=2 ttl=43 time=228 ms
64 bytes from 67.227.226.241: icmp_seq=3 ttl=43 time=455 ms
64 bytes from 67.227.226.241: icmp_seq=4 ttl=43 time=375 ms
^C
--- jimbdo.com ping statistics ---
4 packets transmitted, 4 received, 0% packet loss, time 3003ms
rtt min/avg/max/mdev = 204.909/316.011/455.412/103.768 ms
"
    );

$from = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "sconosciuto";
?>

<html lang="it">
<head>
    <?php include "utils/pages/head.phtml" ?>
</head>
<body>
<section class="section container">

    <article class="message is-danger is-large">
        <div class="message-header">
            <p>Errore lato server!</p>
        </div>
        <div class="message-body content">
            <h1><?= sanitize_html($errore["name"]) ?></h1>
            <p><em><?= sanitize_html($errore["code"]) ?></em></p>
            <blockquote><?= $from ?></blockquote>
            <pre style="height: 80%; overflow-y: scroll; font-size: 60%">
                <?= sanitize_html($errore["what"]) ?>
            </pre>
            <a class="button is-warning" href="mailto:<?= ERROR_MAIL ?>?subject=Problema&body=<?= urlencode($from)?>%0A%0A<?= urlencode($_GET["error"]) ?>">
                <span class="icon">
                    <i class="fa fa-envelope" aria-hidden="true"></i>
                </span>
                <span>
                    Invia segnalazione
                </span>
            </a>
        </div>
    </article>

    <a class="button is-info" href="index.php">
        <span class="icon">
            <i class="fa fa-home"></i>
        </span>
            <span>
            Torna alla pagina principale
        </span>
    </a>
    <a class="button is-info" href="javascript:history.back()">
        <span class="icon">
            <i class="fa fa-arrow-circle-left" aria-hidden="true"></i>
        </span>
        <span>
            Torna alla pagina precedente
        </span>
    </a>
    <a class="button is-danger" href="<?= BASE_DIR ?>utils/logout.php">
        <span class="icon">
            <i class="fa fa-ambulance" aria-hidden="true"></i>
        </span>
        <span>
            Terminare la sessione
        </span>
    </a>
</section>

</body>
</html>
