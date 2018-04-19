<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 19/04/18
 * Time: 19.15
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_STUDENT, \auth\User::UNAUTHORIZED_REDIRECT);

$oauth2 = \auth\connect_token_google($google_client, $user->get_token());

if (!isset($_GET["tirocinio"]))
    redirect('../index.php');

$pubblica = $server->prepare("UPDATE Tirocinio SET visibilita = 'docente' WHERE id=? AND visibilita = 'studente' AND studente = ?");

$pubblica->bind_param(
    "ii",
    $_GET["tirocinio"],
    $user->get_database_id()
);

$pubblica->execute();

redirect("index.php", [
    "tirocinio" => $_GET["tirocinio"],
    "page" => "preview"
]);