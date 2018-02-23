<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 17/02/18
 * Time: 8.27
 */

$json_mode = true;
$force_silent = true;

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$logged = \auth\check_and_redirect(\auth\LEVEL_GOOGLE_TEACHER, false);

if(!$logged)
{
    echo json_encode(["error" => 401, "what" => "unauthorized"]);
    return;
}

\auth\connect_token_google($google_client, $_SESSION["user"]["token"], false);

$server = new \mysqli_wrapper\mysqli();
$return = [];
$skip_filter = !isset($_GET["query"]);
$filter = isset($_GET["query"]) ? "%{$_GET["query"]}%" : "%";

$users = new class(
    $server,
    "SELECT id, nome, cognome, matricola, indirizzo, indirizzo_posta FROM Studente INNER JOIN UtenteGoogle G ON Studente.utente = G.id
      WHERE ? OR nome LIKE ? OR cognome LIKE ? OR matricola LIKE ? OR indirizzo LIKE ? OR indirizzo_posta LIKE ?"
) extends \helper\Pagination
{
    public function compute_rows()
    {
        return PHP_INT_MAX;
    }
};

$users->set_limit(10);
$users->set_current_page(isset($_GET["page"]) ? $_GET["page"] : 0);

$users->bind_param(
    "isssss",
    $skip_filter,
    $filter,
    $filter,
    $filter,
    $filter,
    $filter
);

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