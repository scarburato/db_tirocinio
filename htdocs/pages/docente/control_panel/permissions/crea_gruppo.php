<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 24/03/18
 * Time: 9.57
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$oauth2 = \auth\connect_token_google($google_client, $user->get_token());

$server = new \mysqli_wrapper\mysqli();

$crea_gruppo = $server->prepare("INSERT INTO Gruppo(nome, descrizione) VALUES (?, ?)");
$crea_gruppo->bind_param(
    "ss",
    $_POST["name"],
    $_POST["desc"]
);

$crea_gruppo->execute(true);

redirect("gruppo.php", [
    "group" => $_POST["name"]
]);