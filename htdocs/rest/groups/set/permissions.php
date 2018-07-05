<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 24/03/18
 * Time: 17.36
 */

$json_mode = true;
$force_silent = true;

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";


$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

$server = new \mysqli_wrapper\mysqli();
$permissions = new \auth\PermissionManager($server, $user);

$gruppo = $_POST["group"];

$permissions->check("user.groups", \auth\PermissionManager::UNAUTHORIZED_THROW);

if(empty($_POST["group"]))
    throw new RuntimeException("empty group name", -1);

if($gruppo === "root")
    throw new RuntimeException("You can't do this, please do not try anymore to edit root's proprieties!", -1);

if(!is_array($_POST["permissions"]) && $_POST["permissions"] != 0)
    throw new RuntimeException("You have to supply an array! Send number 0 for empty array :( (Thanks for that PHP)", -1);

$server->autocommit(false);
$drop = $server->prepare("DELETE FROM PermessiGruppo WHERE gruppo = ?");
$drop->bind_param(
    "s",
    $gruppo
);

$drop->execute();
$drop->close();

$insert = $server->prepare("INSERT INTO PermessiGruppo (gruppo, privilegio) VALUES (?, ?)");


$insert->bind_param(
    "ss",
    $gruppo,
    $privilegio
);
foreach ($_POST["permissions"] as $privilegio)
{
    $insert->execute();
}

$insert->close();

$server->commit();

echo json_encode("TODO");