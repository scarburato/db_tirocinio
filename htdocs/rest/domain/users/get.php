<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 23/02/18
 * Time: 16.07
 */

$json_mode = true;
$force_silent = true;

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/fakeService/init.php";

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

\auth\connect_token_google($google_client, $_SESSION["user"]["token"], false);

$server = new \mysqli_wrapper\mysqli();
$permessions = new \auth\PermissionManager($server, $user);

$permessions->check("user.google.add", \auth\PermissionManager::UNAUTHORIZED_THROW);

if(empty($_GET["email"]))
    throw new RuntimeException("Email was not provided!", -1);

build($google_client_2);
$servizi = new Google_Service_Directory($google_client_2);

try
{
    $utente = $servizi->users->get($_GET["email"]);
}
catch (Google_Exception $e)
{
    if($e->getCode() == 404)
        echo json_encode(["found" => false]);
    else
        throw $e;

    return;
}

$esiste_db = $server->prepare("SELECT id FROM UtenteGoogle WHERE SUB_GOOGLE = ?");
$esiste_db->bind_param(
    "s",
    $utente->id
);
$esiste_db->execute();

echo  json_encode([
    "found" => true,
    "no_db" => $esiste_db->fetch() === null,
    "email" => $utente->primaryEmail,
    "orgUnitPath" => $utente->orgUnitPath,
    "thumbnailPhotoUrl" => $utente->thumbnailPhotoUrl,
    "name" => $utente->getName()
]);