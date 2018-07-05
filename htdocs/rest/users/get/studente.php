<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 29/03/18
 * Time: 18.37
 */

$json_mode = true;
$force_silent = true;

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

(new \auth\User())->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

\auth\connect_token_google($google_client, $_SESSION["user"]["token"], false);

$server = new \mysqli_wrapper\mysqli();

if(empty($_GET["email"]))
    throw new RuntimeException("Empty email!", -1);


$docente = $server->prepare(
    "SELECT id, nome, cognome, indirizzo_posta, S.indirizzo
            FROM UtenteGoogle 
            INNER JOIN Studente S ON UtenteGoogle.id = S.utente
           WHERE indirizzo_posta = ?"
);

$docente->bind_param(
    "s",
    $_GET["email"]
);

$docente->execute();

if(($return = $docente->get_result()->fetch_assoc()) === null)
    throw new RuntimeException("User not found in database!", -1);

$docente->close();

echo json_encode($return);