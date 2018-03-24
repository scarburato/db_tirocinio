<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 02/03/18
 * Time: 11.30
 */

$json_mode = true;
$force_silent = true;

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";


(new \auth\User())->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

$server = new \mysqli_wrapper\mysqli();
if(!\auth\check_permission($server, "control.google.users"))
{
    echo json_encode(["error" => 401, "what" => "unauthorized"]);
    return;
};

if(empty($_POST["email"]) && empty($_POST["id"]))
{
    echo json_encode(["error" => -1, "what" => "invalid email or id"]);
    return;
}

if(!is_array($_POST["groups"]) && $_POST["groups"] != 0)
{
    echo json_encode(["error" => -1, "what" => "You have to supply an array! Send number 0 for empty array :("]);
    return;
}

if(empty($_POST["id"]))
{
    $id_stm = $server->prepare("SELECT id FROM UtenteGoogle INNER JOIN Docente D ON UtenteGoogle.id = D.utente
                                  WHERE indirizzo_posta = ?");
    $id_stm->bind_param(
        "i",
        $_POST["email"]
    );

    $id_stm->execute(false);
    $id_stm->bind_result($id);
    if(!$id_stm->fetch())
    {
        echo json_encode("404", "invalid email!");
        return;
    }
    $id_stm->close();
}
else
    $id = $_POST["id"];

$server->autocommit(false);
$drop = $server->prepare("DELETE FROM GruppiApplicati WHERE utente = ?");
$drop->bind_param(
    "i",
    $id
);

$drop->execute();
$drop->close();

$insert = $server->prepare("INSERT INTO GruppiApplicati (utente, gruppo) VALUES (?, ?)");

$gao = [];
$id = (int)$id;
$privilegio = "control.google.permissions";
$insert->bind_param(
    "is",
    $id,
    $privilegio
);
foreach ($_POST["groups"] as $privilegio)
{
    // TODO Controllare perché non funziona l'insert
    $insert->execute();
    array_push($gao, $privilegio);
}

$insert->close();

$server->commit();

echo json_encode([$gao, $id]);