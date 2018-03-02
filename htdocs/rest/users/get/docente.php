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

(new \auth\User())->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

\auth\connect_token_google($google_client, $_SESSION["user"]["token"], false);

$server = new \mysqli_wrapper\mysqli();

if(empty($_GET["email"]))
{
    echo json_encode(["error" => -1, "what" => "invalid email"]);
    return;
}

$docente = $server->prepare(
    "SELECT id, nome, cognome, indirizzo_posta 
            FROM Docente 
            INNER JOIN UtenteGoogle G ON Docente.utente = G.id
           WHERE indirizzo_posta = ?"
);

$docente->bind_param(
    "s",
    $_GET["email"]
);

$docente->execute();

if(($return = $docente->get_result()->fetch_assoc()) === null)
{
    echo json_encode([
        "error" => 404,
        "what" => "user not found!"
    ]);
    return;
}

$return["permessi"] = array();
$docente->close();

$permessi = $server->prepare("SELECT nome, descrizione 
                                      FROM PrivilegiApplicati
                                      INNER JOIN Privilegio P ON PrivilegiApplicati.privilegio = P.nome
                                    WHERE utente = ?");

$permessi->bind_param(
    "i",
    $return["id"]
);

$permessi->execute();
$result = $permessi->get_result();

while($data = $result->fetch_assoc())
    array_push($return["permessi"], $data);

echo json_encode($return);