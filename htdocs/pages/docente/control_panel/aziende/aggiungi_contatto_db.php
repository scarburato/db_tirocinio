<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 21/05/18
 * Time: 18.44
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));
$permissions = new \auth\PermissionManager($server, $user);

$permissions->check("factory.contacts.create", \auth\PermissionManager::UNAUTHORIZED_THROW);

$info = $server->prepare("SELECT Azienda.id, nominativo 
                                  FROM Azienda 
                                  INNER JOIN Classificazioni C ON Azienda.classificazione = C.id
                                  INNER JOIN CodiceAteco C2 ON Azienda.ateco = C2.id
                                  WHERE Azienda.id = ?");

$info->bind_param("i", $_POST["id"]);
$info->execute();
$info->bind_result($id, $nome);
$info->store_result();
if($info->fetch() !== true)
	throw new RuntimeException("Azienda non esistente!", -1);


// Stringhe vuote sono null!
if($_POST["nome"] === "")
	$_POST["nome"] = null;

if($_POST["cognome"] === "")
	$_POST["cognome"] = null;

if($_POST["posta"] === "")
	$_POST["posta"] = null;

if($_POST["tel"] === "")
	$_POST["tel"] = null;

if($_POST["qualifica"] === "")
	$_POST["qualifica"] = null;

if($_POST["ruolo"] === "")
	$_POST["ruolo"] = null;

$insert = $server->prepare(
	"INSERT INTO Contatto(azienda, nome, cognome, email, telefono, FAX, qualifica, ruoloAziendale) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

$insert->bind_param(
	"isssssss",
	$user->get_database_id(),
	$_POST["nome"],
	$_POST["cognome"],
	$_POST["posta"],
	$_POST["tel"],
	$_POST["fax"],
	$_POST["qualifica"],
	$_POST["ruolo"]
);

try
{
	$insert->execute();
}
catch (\mysqli_wrapper\sql_exception $e)
{
	redirect("aggiungi_contatto.php", [
		"id" => $id,
		"error" => $e->get_error_list_as_json()
	]);
}

redirect("/pages/docente/azienda/contatto", [
	"id" => $insert->insert_id
]);