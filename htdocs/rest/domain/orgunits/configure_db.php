<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 28/02/18
 * Time: 10.01
 */

$json_mode = true;
$force_silent = true;

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

(new \auth\User())->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

$server = new \mysqli_wrapper\mysqli();
if(!\auth\check_permission($server, "control.training.create"))
{
    echo json_encode(["error" => 401, "what" => "unauthorized"]);
    return;
};

if(empty($_GET["orgunits"]))
{
    echo json_encode(["error" => -1, "what" => "You have to supply an array!"]);
    return;
}

$unita = json_decode($_GET["orgunits"], JSON_OBJECT_AS_ARRAY);
$error = json_last_error();
if($error !== JSON_ERROR_NONE)
{
    echo json_encode(["error" => $error, "what" => json_last_error_msg()]);
}

echo json_encode($unita);