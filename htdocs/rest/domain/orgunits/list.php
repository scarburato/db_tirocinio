<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 20/02/18
 * Time: 19.17
 */

$json_mode = true;
$force_silent = true;

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/fakeService/init.php";

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

$server = new \mysqli_wrapper\mysqli();
$permissions = new \auth\PermissionManager($server, $user);

$permissions->check("user.google.orgunits", \auth\PermissionManager::UNAUTHORIZED_THROW);

build($google_client_2);
$servizi = new Google_Service_Directory($google_client_2);

$orgunits = $servizi->orgunits->listOrgunits("C030q1w53", [
    "type" => "all"
]);

echo json_encode($orgunits->getOrganizationUnits(), JSON_UNESCAPED_UNICODE);