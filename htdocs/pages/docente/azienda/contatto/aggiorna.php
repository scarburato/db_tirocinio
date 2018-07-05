<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 27/05/18
 * Time: 10.33
 */

$json_mode = true;


require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";
$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

if($_GET["newend"] === "")
	$_GET["newend"] = null;

$permissions = new \auth\PermissionManager($server, $user);
$permissions->check("factory.intouch", \auth\PermissionManager::UNAUTHORIZED_THROW);

$aggiorna = $server->prepare(/** @lang MySQL */
	"UPDATE EntratoInContatto SET fine = ? WHERE docente = ? AND contatto = ? AND inizio = ?");
$id = $user->get_database_id();
$aggiorna->bind_param(
	"siis",
	$_GET["newend"],
	$id,
	$_GET["contatto"],
	$_GET["inizio"]
);

$aggiorna->execute();

echo json_encode($aggiorna->num_rows);