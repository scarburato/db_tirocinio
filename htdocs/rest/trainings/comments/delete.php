<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 07/04/18
 * Time: 22.05
 */

$json_mode = true;
$force_silent = true;

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

\auth\connect_token_google($google_client, $_SESSION["user"]["token"], false);

if(empty($_GET["comment"]))
    throw new RuntimeException("You have to supply an id!", -1);

$server = new \mysqli_wrapper\mysqli();
$permission_manager = new \auth\PermissionManager($server, $user);

$permission_manager->check("train.comments.delete", \auth\PermissionManager::UNAUTHORIZED_THROW);

$tutti_tir = $permission_manager->check("train.readall");

$eliminazione = $server->prepare("DELETE FROM Commento WHERE id = ? AND (? OR EXISTS(
    SELECT T.id FROM Tirocinio T WHERE T.docenteTutore = ? AND tirocinio = ?
))");

$eliminazione->bind_param(
    "iiii",
    $_GET["comment"],
    $tutti_tir,
    $user->get_database_id(),
    $_GET["comment"]
);

$eliminazione->execute();

if($eliminazione->errno)
    throw new RuntimeException($eliminazione->error, $eliminazione->errno);

echo json_encode([
    "rows" => $eliminazione->affected_rows
]);