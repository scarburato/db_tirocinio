<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 14/02/18
 * Time: 19.29
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();
$aggiungi = $server->prepare("INSERT INTO UtenteGoogle(SUB_GOOGLE, nome, cognome, indirizzo_posta) VALUES  (?, ?, ?, ?);");
$news = array();

for($i = 0; $i < 2000*15; $i++)
{
    $id = time() . $i;
    $posta = $id . "@itispisa.gov.it";
    $nome = "N" . $id;
    $aggiungi->bind_param(
        "ssss",
        $id,
        $nome,
        $id,
        $posta)
    ;
    $aggiungi->execute(true);

    array_push($news, $aggiungi->insert_id);
}

$aggiungi->close();

$aggiungi = $server->prepare("INSERT INTO Studente(utente) VALUES (?)");

foreach ($news as $id)
{
    $aggiungi->bind_param(
        "i",
        $id
    );
    $aggiungi->execute(true);
}