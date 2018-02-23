<?php
require_once "utils/lib.hphp";
require_once "fakeService/init.php";
require_once "utils/auth.hphp";

/* TODO rinominare in auth.php
 * (È necessario modificare impostazione da Google :P)
 */

// Ottenere il token d'accesso
$google_client->fetchAccessTokenWithAuthCode($_GET["code"]);
$token = $google_client->getAccessToken();

$google_client->setAccessToken($token);

$oauth2 = new \Google_Service_Oauth2($google_client);
$user = $oauth2->userinfo->get();

// Controllo del domino
if($user->hd !== "itispisa.gov.it")
{
    // Disconessione
    $google_client->revokeToken();
    header("Location: index.php?wrong_domain=true");
    die("Dominio errato! Non si dovrobbe arrivare a questo punto");
}

// Variabili
$id = null;

// Aggiunta al servente¡
$server = new \mysqli_wrapper\mysqli();

// Cerco di ottenre l'ID se esiste altrimenti lo aggiungo
$id_stm = $server->prepare("SELECT id FROM UtenteGoogle WHERE SUB_GOOGLE = ?");
$id_stm->bind_param(
    "s",
    $user->id
);

$id_stm->execute(true);
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
    $user->givenName,
    $user->familyName,
    $user->email,
    $user->picture,
    $user->id
    );

$operazione->execute(true);
$operazione->bind_result($id);
$operazione->fetch();

if($id === null)
    $id = $operazione->insert_id;

$operazione->close();

// Controllo tipologia d'utenza
build($google_client_2);
$servizi = new Google_Service_Directory($google_client_2);

$utente = $servizi->users->get($user->email);

$controllo = $server->prepare("SELECT tipo FROM UnitaOrganizzativa
WHERE INSTR(?, unita_organizzativa) = 1");

$controllo->bind_param(
    "s",
    $utente->orgUnitPath
);

$controllo->execute(true);
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

if($user->email === "dario.pagani@itispisa.gov.it")
    $utente_studente = $utente_docente = true;

if($utente_docente)
{
    $operazione = $server->prepare("INSERT INTO Docente(utente) VALUES (?)");
    $operazione->bind_param(
        "i",
        $id
    );
    $operazione->execute(false);
    $operazione->close();
}

if($utente_studente)
{
    $operazione = $server->prepare("INSERT INTO Studente(utente) VALUES (?)");
    $operazione->bind_param(
        "i",
        $id
    );
    $operazione->execute(false);
    $operazione->close();
}

$_SESSION["user"]["id"] = $id;
$_SESSION["user"]["token"] = $token;

if($utente_studente && $utente_docente)
    $_SESSION["user"]["type"] = \auth\LEVEL_GOOGLE_BOTH;
elseif ($utente_docente)
    $_SESSION["user"]["type"] = \auth\LEVEL_GOOGLE_TEACHER;
elseif ($utente_studente)
    $_SESSION["user"]["type"] = \auth\LEVEL_GOOGLE_STUDENT;
else
{
    $_SESSION["user"]["type"] = \auth\LEVEL_GOOGLE_UNAUTHORIZED;
    /** @noinspection PhpUnhandledExceptionInspection */
    throw new Exception("Current user is not allowed to do anything :P");
}

header("Location: ambiguita.php");