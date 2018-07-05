<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 26/02/18
 * Time: 11.11
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();
$user = new auth\User();
$user->is_authorized(\auth\LEVEL_FACTORY, \auth\User::UNAUTHORIZED_THROW);

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
	redirect("crea.php", [
		"error" => $e->get_error_list_as_json()
	]);
}

redirect("../");