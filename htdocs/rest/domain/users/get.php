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

(new \auth\User())->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

$server = new \mysqli_wrapper\mysqli();
if(!\auth\check_permission($server, "control.google.users.readonly"))
{
    echo json_encode(["error" => 401, "what" => "unauthorized"]);
    return;
};

if(empty($_GET["email"]))
{
    echo json_encode(["error" => -1, "what" => "invalid email"]);
    return;
}

build($google_client_2);
$servizi = new Google_Service_Directory($google_client_2);

try
{
    $utente = $servizi->users->get($_GET["email"]);
}
catch (Google_Exception $e)
{
    if($e->getCode() == 404)
        echo json_encode(["error" => null, "found" => false]);
    else
        throw $e;

    return;
}

$esiste_db = $server->prepare("SELECT id FROM UtenteGoogle WHERE SUB_GOOGLE = ?");
$esiste_db->bind_param(
    "s",
    $utente->id
);
$esiste_db->execute(false);

echo  json_encode([
    "error" => null,
    "found" => true,
    "no_db" => $esiste_db->fetch() === null,
    "email" => $utente->primaryEmail,
    "orgUnitPath" => $utente->orgUnitPath,
    "thumbnailPhotoUrl" => $utente->thumbnailPhotoUrl,
    "name" => $utente->getName()
]);