<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 25/02/18
 * Time: 20.20
 */

$json_mode = true;
$force_silent = true;

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/fakeService/init.php";

(new \auth\User())->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

$server = new \mysqli_wrapper\mysqli();
if(!\auth\check_permission($server, "control.google.users"))
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

// TODO