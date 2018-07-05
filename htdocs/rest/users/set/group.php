<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 02/03/18
 * Time: 11.30
 */

$json_mode = true;
$force_silent = true;

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

require_once dirname(__FILE__) . "/../functions.hphp";

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

$server = new \mysqli_wrapper\mysqli();
$permission_manager = new \auth\PermissionManager($server, $user);
$permission_manager->check("user.groups", \auth\PermissionManager::UNAUTHORIZED_THROW);

if(!is_array($_POST["groups"]) && $_POST["groups"] != 0)
    throw new RuntimeException("You have to supply an array! Send number 0 for empty array :( (Thanks for that PHP)", -1);

$id = get_id($server, $_POST);

$server->autocommit(false);
$drop = $server->prepare("DELETE FROM GruppiApplicati WHERE utente = ?");
$drop->bind_param(
    "i",
    $id
);

$drop->execute();
$drop->close();

$insert = $server->prepare("INSERT INTO GruppiApplicati (utente, gruppo) VALUES (?, ?)");


$insert->bind_param(
    "is",
    $id,
    $privilegio
);
foreach ($_POST["groups"] as $privilegio)
{
    $insert->execute();
}

$insert->close();

$server->commit();

echo json_encode("TODO");