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
$google_user = new \auth\GoogleConnection($user); $oauth2 = $google_user->getUserProps();

$server = new \mysqli_wrapper\mysqli();

$crea_gruppo = $server->prepare("INSERT INTO Gruppo(nome, descrizione) VALUES (?, ?)");
$crea_gruppo->bind_param(
    "ss",
    $_POST["name"],
    $_POST["desc"]
);

$crea_gruppo->execute();

redirect("gruppo.php", [
    "group" => $_POST["name"]
]);