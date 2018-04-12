<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 28/02/18
 * Time: 10.01
 */

$json_mode = true;
$force_silent = true;

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

$server = new \mysqli_wrapper\mysqli();
$permissions = new \auth\PermissionManager($server, $user);

$permissions->check("user.google.orgunits", \auth\PermissionManager::UNAUTHORIZED_THROW);

if(!is_array($_POST["orgunits"]))
    throw new RuntimeException("You have to provide an array of orguinits!", -1);

$server->autocommit(false);
$drop = $server->prepare("DELETE FROM UnitaOrganizzativa WHERE TRUE ");
$drop->execute();
$drop->close();

$add = $server->prepare("INSERT INTO UnitaOrganizzativa(tipo, unita_organizzativa) VALUES (?, ?)");

$add->bind_param(
    "ss",
    $tipo,
    $path
);

foreach ($_POST["orgunits"] as $value)
{
    $tipo = $value["type"];
    $path = $value["path"];
    $add->execute();
}

$add->close();

$server->commit();

echo json_encode($_POST["orgunits"], JSON_UNESCAPED_UNICODE);