<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 02/02/18
 * Time: 17.09
 */

$force_silent =true;
$json_mode = true;

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) ."/utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GOOGLE_TEACHER);
$oauth2 = \auth\connect_token_google($google_client, $_SESSION["user"]["token"]);

$return = array();
$server = new \mysqli_wrapper\mysqli();

//$codice_fiscale = $server->prepare("SELECT COUNT(*) FROM Azienda WHERE codiceFiscale = ?");
$codice_fiscale = $server->prepare( "SELECT EXISTS(
    SELECT id FROM Azienda WHERE codiceFiscale = ?
    )");

$codice_fiscale->bind_param(
    "s",
    $_POST["cf"]
);

$codice_fiscale->execute();
$codice_fiscale->bind_result($count);
$codice_fiscale->fetch();

$return["esiste"] = ($count == 1);
echo json_encode($return);