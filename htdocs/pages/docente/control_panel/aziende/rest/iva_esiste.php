<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 02/02/18
 * Time: 19.02
 */

$force_silent =true;
$json_mode = true;

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) ."/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$oauth2 = \auth\connect_token_google($google_client, $user->get_token());

$return = array();
$server = new \mysqli_wrapper\mysqli();

$codice_fiscale = $server->prepare("SELECT COUNT(*) FROM Azienda WHERE IVA = ?");

$codice_fiscale->bind_param(
    "s",
    $_POST["iva"]
);

$codice_fiscale->execute();
$codice_fiscale->bind_result($count);
$codice_fiscale->fetch();

$return["esiste"] = ($count > 0);

echo json_encode($return);