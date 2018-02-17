<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 29/01/18
 * Time: 11.15
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GOOGLE_TEACHER);
\auth\connect_token_google($google_client, $_SESSION["user"]["token"], "./../../../../");

$server = new \mysqli_wrapper\mysqli();

$perdono = $server->prepare(
    "UPDATE AziendeTentativiAccesso
    SET tentativi_falliti = 0
    WHERE indirizzo_rete = ?;"
);

$perdono->bind_param(
    "s",
    inet_pton($_GET["indirizzo"])
);

$perdono->execute();

header("Location: index.php");