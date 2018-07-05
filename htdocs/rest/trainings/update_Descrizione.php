<?php
/**
 * Created by Atom.
 * User: Enrico
 * Date: 28/02/18
 * Time: 11.30
 */

$json_mode = true;
$force_silent = true;

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_STUDENT, \auth\User::UNAUTHORIZED_THROW);

\auth\connect_token_google($google_client, $_SESSION["user"]["token"], false);

$server = new \mysqli_wrapper\mysqli();
$return = [];
$newDescription = $_POST['contenuto'];

if (empty($newDescription))
    throw new RuntimeException("Missing contenuto!", -1);

if (empty($_POST['tirocinio']))
    throw new RuntimeException("Missing tirocinio ID!", -1);

$update = $server->prepare("UPDATE Tirocinio SET descrizione=? WHERE id = ? AND studente = ? AND dataInizio < CURRENT_DATE AND visibilita <> 'azienda'");

$update->bind_param('sii', $newDescription, $_POST['tirocinio'], $user->get_database_id());

$update->execute();

$return["success"]=true;
$return["md5"]=md5($newDescription);
$return["last_edit"] = date('Y-m-d G:i:s'); // LoL, quasi sicuramente non sarà mai identeica a quella nella base di dati :PPPP

echo json_encode($return, JSON_UNESCAPED_UNICODE);
