<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 07/02/18
 * Time: 14.13
 */

error_reporting(0);
$force_silent =true;
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) ."/utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GOOGLE_TEACHER);
$user = \auth\connect_token_google($google_client, $_SESSION["user"]["token"], "./../../../../", $oauth2);

const EDITABLE_VALID_COLUMNS = ["IVA", "codiceFiscale", "nominativo", "classificazione", "ateco", "dimensione", "gestione", "no_accessi"];
$return = array();
if($_POST["method"] === "columns")
{
    echo json_encode(EDITABLE_VALID_COLUMNS, JSON_PRETTY_PRINT);
    return;
}
else if($_POST["method"] === "edit")
{
    if(array_search($_POST["column"], EDITABLE_VALID_COLUMNS) === false)
    {
        $return["error"] = true;
        $return["error_type"] = "Invalid column!";
        echo json_encode($return);
    }
}