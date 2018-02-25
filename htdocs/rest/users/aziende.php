<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 20/02/18
 * Time: 18.15
 */

$json_mode = true;
$force_silent = true;

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

(new \auth\User())->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

\auth\connect_token_google($google_client, $_SESSION["user"]["token"], false);

$server = new \mysqli_wrapper\mysqli();
$return = [];
$skip_filter = !isset($_GET["query"]);
$filter = isset($_GET["query"]) ? "%{$_GET["query"]}%" : "%";

$users = new class(
    $server,
    "SELECT Azienda.id AS 'id', nominativo, codiceFiscale, IVA, gestione, dimensione, C.descrizione AS 'classificazione', C2.cod2007 AS 'ateco' FROM Azienda
      INNER JOIN Classificazioni C ON Azienda.classificazione = C.id
      INNER JOIN CodiceAteco C2 ON Azienda.ateco = C2.id"
) extends \helper\Pagination
{
    public function compute_rows()
    {
        return PHP_INT_MAX;
    }
};

$users->set_limit(10);
$users->set_current_page(isset($_GET["page"]) ? $_GET["page"] : 0);

/*$users->bind_param(
    "isssss",
    $skip_filter,
    $filter,
    $filter,
    $filter,
    $filter,
    $filter
);*/

$users->execute();
$result = $users->get_result();
$return["current_page"] = $users->get_current_page();
$return["previus_page"] = $users->has_previus_page() ? $users->get_current_page() - 1 : null;
$return["next_page"] = $users->has_next_page() ? $users->get_current_page() + 1 : null;
$return["last_page"] = null;
$return["data_rows"] = $result->num_rows;
$return["data"] = array();

while($data = $result->fetch_assoc())
    array_push($return["data"], $data);

echo json_encode($return, JSON_UNESCAPED_UNICODE);