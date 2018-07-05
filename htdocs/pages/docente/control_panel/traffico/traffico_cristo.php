<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 29/01/18
 * Time: 11.15
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$google_user = new \auth\GoogleConnection($user); $oauth2 = $google_user->getUserProps();

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

$successo = $perdono->execute();

redirect("index.php", [
    "success" => $successo
]);