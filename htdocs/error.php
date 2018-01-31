<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 19/01/18
 * Time: 18.56
 */
require_once "utils/const.hphp";
$page = "Pagina d'errore";

if(isset($_GET["error"]))
    $errore = json_decode(urldecode($_GET["error"]),true);
else
    $errore = array(
        "name" => "Problema generico",
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
    )
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
        <h1><?= $errore["name"] ?></h1>
        <p><em><?= $errore["code"]?></em></p>
            <pre><?= $errore["what"] ?></pre>
    </div>
</article>

<a class="button is-info" href="index.php">
    Torna alla pagina principale!
</a>
</section>

</body>
</html>
