<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 20/02/18
 * Time: 19.17
 */

$json_mode = true;
$force_silent = true;

//require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/const.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

require_once dirname(__FILE__) . "/../../../vendor/autoload.php";

// TODO Vedere di far funzionare 'sto account di servizio!

//$logged = \auth\check_and_redirect(\auth\LEVEL_GOOGLE_TEACHER, false);

//if(!$logged)
//{
//    echo json_encode(["error" => 401, "what" => "unauthorized"]);
//    return;
//}

//$server = new \mysqli_wrapper\mysqli();
//if(!\auth\check_permission($server, "control.training.create"))
//{
//    echo json_encode(["error" => 401, "what" => "unauthorized"]);
//}

//\auth\connect_token_google($google_client, $_SESSION["user"]["token"], false);

echo json_encode([
    "code" => 501,
    "what" => "Non funzionerà maii!"
]);

return;

putenv( 'GOOGLE_APPLICATION_CREDENTIALS=' . dirname(__FILE__) . "/../../../client_" .
    "secret_api.json" );

$api = new Google_Client();

//$api->setAccessToken($_SESSION["user"]["token"]);
//$api->setSubject("C030q1w53");
$api->useApplicationDefaultCredentials();
$api->setIncludeGrantedScopes(true);
$api->addScope("https://www.googleapis.com/auth/admin.directory.orgunit.readonly");

//$api->setAccessToken($_SESSION["user"]["token"]);

$servizi = new Google_Service_Directory($api);

$orgunits = $servizi->orgunits->listOrgunits("C030q1w53", [
    "type" => "all"
]);

echo json_encode($orgunits->getOrganizationUnits(), JSON_UNESCAPED_UNICODE);