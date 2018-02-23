<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 17/02/18
 * Time: 15.35
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GOOGLE_TEACHER);
$oauth2 = \auth\connect_token_google($google_client, $_SESSION["user"]["token"]);

$server = new \mysqli_wrapper\mysqli();
\auth\check_permission($server, "control.training.create");

// TODO Scrivere controlli
// Controllo validità campi
$errori = array();

if(strlen($_POST["studente"]) < 1)
    array_push($errori, "studente empty");

if(strlen($_POST["azienda"]) < 1)
    array_push($errori, "azienda empty");

if(strlen($_POST["docente"]) < 1)
    array_push($errori, "docente empty");

if(strlen($_POST["data_inizio"]) < 1)
    array_push($errori, "data_inizio empty");

if(isset($_POST["data_fine"]) && $_POST["data_fine"] <= $_POST["data_inizio"])
    array_push($errori, "data_fine is GREATER that data_inizio");

if(count($errori) > 0)
    redirect("aggiungi.php", [
        "errors" => json_encode($errori, JSON_PRETTY_PRINT)
    ]);

$tutore = empty($_POST["tutore"]) ? null : $_POST["tutore"];
$data_inizio = empty($_POST["data_inizio"]) ? null : $_POST["data_inizio"];
$data_fine = empty($_POST["data_fine"]) ? null : $_POST["data_fine"];

$insert = $server->prepare("INSERT INTO Tirocinio(studente, azienda, docenteTutore, tutoreAziendale, dataInizio, dataTermine)
                                            VALUES (?, ?, ?, ?, ?, ?)");

$insert->bind_param(
    "iiiiss",
    $_POST["studente"],
    $_POST["azienda"],
    $_POST["docente"],
    $tutore,
    $data_inizio,
    $data_fine
);

$fail = !$insert->execute();

if($fail)
{
    redirect("aggiungi.php", [
        "errors" => json_encode($server->error_list, JSON_PRETTY_PRINT)
    ]);
}

redirect("aggiungi.php");