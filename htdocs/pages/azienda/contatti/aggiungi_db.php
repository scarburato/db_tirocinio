<?phphttps://pgnfba.dtdns.net:10101/pages/docente/tirocini/aggiungi.php
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
$user->is_authorized(\auth\LEVEL_FACTORY, \auth\User::UNAUTHORIZED_REDIRECT);

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

$insert->execute(true);

redirect("../");