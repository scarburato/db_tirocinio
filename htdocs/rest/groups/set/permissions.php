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


(new \auth\User())->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

$server = new \mysqli_wrapper\mysqli();
$gruppo = $_POST["group"];

if(!\auth\check_permission($server, "control.google.users"))
{
    echo json_encode(["error" => 401, "what" => "unauthorized"]);
    return;
};

if(empty($_POST["group"]))
{
    echo json_encode(["error" => -1, "what" => "empty group name!"]);
    return;
}

if($gruppo === "root")
{
    echo json_encode(["error" => -1, "what" => "Sorry ma'am you can't"]);
    return;
}

if(!is_array($_POST["permissions"]) && $_POST["permissions"] != 0)
{
    echo json_encode(["error" => -1, "what" => "You have to supply an array! Send number 0 for empty array :("]);
    return;
}

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