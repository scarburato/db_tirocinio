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

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

\auth\connect_token_google($google_client, $_SESSION["user"]["token"], false);

$server = new \mysqli_wrapper\mysqli();
$permessions = new \auth\PermissionManager($server, $user);

$permessions->check("user.google.add", \auth\PermissionManager::UNAUTHORIZED_THROW);

if(empty($_GET["email"]))
    throw new RuntimeException("Email was not provided!", -1);

build($google_client_2);

$servizi = new Google_Service_Directory($google_client_2);

try
{
    $utente = $servizi->users->get($_GET["email"]);
}
catch (Google_Exception $e)
{
    if ($e->getCode() == 404)
        echo json_encode(["found" => false]);
    else
        throw $e;

    return;
}
// Cerco di ottenre l'ID se esiste altrimenti lo aggiungo
$id_stm = $server->prepare("SELECT id FROM UtenteGoogle WHERE SUB_GOOGLE = ?");
$id_stm->bind_param(
    "s",
    $utente->id
);

$id_stm->execute();
$id_stm->store_result();
$id_stm->bind_result($id);
// Se c'è una riga allora aggiorno i dati altrimenti creo
if($id_stm->fetch())
    $operazione = $server->prepare(
        "UPDATE UtenteGoogle SET nome = ?, cognome = ?, indirizzo_posta = ?, fotografia = ? WHERE SUB_GOOGLE = ?"
    );
else
    $operazione = $server->prepare("INSERT INTO UtenteGoogle(nome, cognome, indirizzo_posta, fotografia, SUB_GOOGLE) VALUES  (?,?,?,?,?);");

$operazione->bind_param(
    "sssss",
    $utente->getName()->givenName,
    $utente->getName()->familyName,
    $utente->primaryEmail,
    $utente->thumbnailPhotoUrl,
    $utente->id
);

$operazione->execute();
$operazione->bind_result($id);
$operazione->fetch();

if($id === null)
    $id = $operazione->insert_id;

$return["id"] = $id;

$operazione->close();

// Retrive
if($google_client === null)
{
    $return["type"] = \auth\LEVEL_GOOGLE_UNAUTHORIZED;
    return $return;
}

$controllo = $server->prepare("SELECT tipo FROM UnitaOrganizzativa WHERE INSTR(?, unita_organizzativa) = 1");

$controllo->bind_param(
    "s",
    $utente->orgUnitPath
);

$controllo->execute();
$controllo->bind_result($tipo);

while($controllo->fetch())
    switch ($tipo)
    {
        case "docente":
            $utente_docente = true;
            break;
        case "studente":
            $utente_studente = true;
            break;
        case "ambedue":
            $utente_docente = $utente_studente = true;
            break;
    }

if($utente->primaryEmail === "dario.pagani@itispisa.gov.it")
    $utente_studente = $utente_docente = true;

if($utente_docente)
{
    $operazione = $server->prepare("INSERT INTO Docente(utente) VALUES (?)");
    $operazione->bind_param(
        "i",
        $id
    );
    $operazione->execute();
    $operazione->close();
}

if($utente_studente)
{
    $operazione = $server->prepare("INSERT INTO Studente(utente) VALUES (?)");
    $operazione->bind_param(
        "i",
        $id
    );
    $operazione->execute();
    $operazione->close();
}

if($utente_studente && $utente_docente)
    $return["type"] = \auth\LEVEL_GOOGLE_BOTH;
elseif ($utente_docente)
    $return["type"] = \auth\LEVEL_GOOGLE_TEACHER;
elseif ($utente_studente)
    $return["type"] = \auth\LEVEL_GOOGLE_STUDENT;
else
    $return["type"] = \auth\LEVEL_GOOGLE_UNAUTHORIZED;

echo json_encode($return, JSON_UNESCAPED_UNICODE);