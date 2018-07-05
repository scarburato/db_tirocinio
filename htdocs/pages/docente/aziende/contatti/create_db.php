<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 10/04/18
 * Time: 16.22
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

$google_user = new \auth\GoogleConnection($user); $oauth2 = $google_user->getUserProps();

$permissions = new \auth\PermissionManager($server, $user);

try
{
    $permissions->check("factory.intouch", \auth\PermissionManager::UNAUTHORIZED_THROW);

    if (empty($_POST["contatto"]))
        throw new LogicException("Missing param contatto");

    if (empty($_POST["data_inizio"]))
        throw new LogicException("Missing param data_inizio!");

    $fine = $_POST["data_fine"] === "" ? null : $_POST["data_fine"];

    $richiesta = $server->prepare("INSERT INTO EntratoInContatto(docente, contatto, inizio, fine) VALUES (?, ?, ?, ?)");

    $richiesta->bind_param(
        "iiss",
        $user->get_database_id(),
        $_POST["contatto"],
        $_POST["data_inizio"],
        $fine
    );

    $richiesta->execute();
}
catch (Throwable $e)
{
    redirect("create.php", [
        "errors" => $e->getMessage()
    ]);
}

redirect("index.php");
