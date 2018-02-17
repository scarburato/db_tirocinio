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
$aggiungi = $server->prepare("INSERT INTO UtenteGoogle(SUB_GOOGLE, nome, cognome, indirizzo_posta, fotografia) VALUES  (?,?,?,?,?);");

for($i = 52*2; $i < 52*5; $i++)
{
    $aggiungi->bind_param(
        "sssss",
        $i,
        $i,
        $i,
        $i,
        $i
    );
    $aggiungi->execute(true);
}