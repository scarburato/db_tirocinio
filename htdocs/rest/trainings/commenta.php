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
$comment_datum = $_POST['contenuto'];
if (!isset($comment_datum))
{
  // errore, è impossibile che un utente comune passi NULL via questa POST, è bloccato prima
}

$inser = $server->prepare("INSERT INTO Commento (tirocinio, autore, testo)
  VALUES (?,?,?)");

$inser->bind_param('iis', $_POST['tirocinio'], $user->get_database_id(), $comment_datum);
$inser->execute();

$return["errore"]=$inser->error ? $inser->error : NULL;
$return['tir']=$_POST['tirocinio'];
$return['user']=$user->get_database_id();
$return['datum']=$comment_datum;

echo json_encode($return, JSON_UNESCAPED_UNICODE);
