<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 24/03/18
 * Time: 17.58
 */


require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
\auth\connect_token_google($google_client, $user->get_token());

if(empty($_GET["group"]))
    redirect("gruppi.php");

if($_GET["group"] === "root")
    die("\"I'm sorry Dave, I'm afraid I can't do that\" - Hal 9000");

$server->autocommit(false);

// Cancellazione partecipazione ai gruppi
$delete = $server->prepare("DELETE FROM GruppiApplicati WHERE gruppo = ?");
$delete->bind_param(
    "s",
    $_GET["group"]
);
$delete->execute();
$delete->close();

// Cancellazione associozioni
$delete = $server->prepare("DELETE FROM PermessiGruppo WHERE gruppo = ?");
$delete->bind_param(
    "s",
    $_GET["group"]
);
$delete->execute();
$delete->close();

// Cancellazione finale
$delete = $server->prepare("DELETE FROM Gruppo WHERE nome = ?");
$delete->bind_param(
    "s",
    $_GET["group"]
);
$delete->execute();
$delete->close();

$server->commit();

redirect("gruppi.php");
