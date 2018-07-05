<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 27/05/18
 * Time: 15.06
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

$int = $server->prepare("UPDATE Studente SET matricola = ? WHERE utente = ?");
$int->bind_param("si", $_GET["matricola"], $_GET["studente"]);
$int->execute();

redirect("info.php", [
	"utente" => $_GET["studente"]
]);