<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 12/05/18
 * Time: 9.08
 */

$json_mode = true;
$force_silent = true;

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

$server = new \mysqli_wrapper\mysqli();

$permissions = new \auth\PermissionManager($server, $user);
$permissions->check("factory.intouch", \auth\PermissionManager::UNAUTHORIZED_THROW);

if (empty($_GET["contatto"]))
	throw new RuntimeException("Missing param contatto");

if (empty($_GET["inizio"]))
	throw new RuntimeException("Missin param inizio");

if (empty($_GET["fine"]))
	$_GET["fine"] = null;

$sovrappone = $server->prepare(/** @lang MySQL */
	"SELECT sovrapponeEvento(?, ?, ?, ?)");
$sovrappone->bind_param(
	"iiss",
	$user->get_database_id(),
	$_GET["contatto"],
	$_GET["inzio"],
	$_GET["fine"]
);

$sovrappone->execute();
$sovrappone->bind_result($isSovrappone);

if ($sovrappone->fetch() !== true)
	throw new RuntimeException("This is impossible!");

echo json_encode([
	"sovrappone" => $isSovrappone ? true : false
], JSON_UNESCAPED_UNICODE);