<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 28/02/18
 * Time: 10.51
 */

$json_mode = true;
$force_silent = true;

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

require_once dirname(__FILE__) . "/../functions.hphp";

(new \auth\User())->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

\auth\connect_token_google($google_client, $_SESSION["user"]["token"], false);

$server = new \mysqli_wrapper\mysqli();

$id = get_id($server, $_GET);

$docente = $server->prepare(
    "SELECT id, nome, cognome, indirizzo_posta 
            FROM Docente 
            INNER JOIN UtenteGoogle G ON Docente.utente = G.id
           WHERE G.id = ?"
);

$docente->bind_param(
    "s",
    $id
);

$docente->execute();

if(($return = $docente->get_result()->fetch_assoc()) === null)
    throw new RuntimeException("User not found in database!", -1);

$docente->close();

$permessi = $server->prepare("SELECT nome, descrizione 
                                      FROM GruppiApplicati
                                      INNER JOIN Gruppo P ON GruppiApplicati.gruppo = P.nome
                                    WHERE utente = ?");

$permessi->bind_param(
    "i",
    $id
);

$permessi->execute();
$result = $permessi->get_result();

$return["gruppi"] = [];
while($data = $result->fetch_assoc())
    array_push($return["gruppi"], $data);

echo json_encode($return);