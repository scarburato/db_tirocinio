<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 10/04/18
 * Time: 14.39
 */

$json_mode = true;
$force_silent = true;

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_THROW);

\auth\connect_token_google($google_client, $_SESSION["user"]["token"], false);

$server = new \mysqli_wrapper\mysqli();

$permissions = new \auth\PermissionManager($server, $user);
$permissions->check("factory.intouch", \auth\PermissionManager::UNAUTHORIZED_THROW);

$return = [];

$skip_filter = !isset($_GET["query"]);
$filter = isset($_GET["query"]) ? "%{$_GET["query"]}%" : "%";

$skip_factory = !isset($_GET["azienda"]);
$factory = isset($_GET["azienda"]) ? $_GET["azienda"] : 0;

$return["dbg"] = $factory;
$return["dbgbis"] = $skip_factory;


//if(!is_integer($factory))
//    throw new RuntimeException("azienda MUST the internal ID!");

$contacts = new class(
    $server,
    "SELECT C.id AS 'id', C.nome, C.cognome, C.email, C.telefono, C.FAX, C.qualifica, C.ruoloAziendale, A.id AS 'Azienda', A.nominativo AS 'AziendaNome',
        EXISTS(SELECT contatto FROM EntratoInContatto WHERE contatto = C.id AND inizio <= CURRENT_DATE() AND (fine IS NULL OR fine > CURRENT_DATE())) AS 'occupato'
      FROM Contatto C
      INNER JOIN Azienda A on C.azienda = A.id
    WHERE ? OR A.id = ?"
) extends \helper\Pagination
{
    public function compute_rows(): int
    {
        return PHP_INT_MAX;
    }
};

$contacts->set_limit(10);
$contacts->set_current_page(isset($_GET["page"]) ? $_GET["page"] : 0);

$contacts->bind_param(
    "ii",
    $skip_factory,
    $factory
);

$contacts->execute();

$result = $contacts->get_result();

$return["current_page"] = $contacts->get_current_page();
$return["previus_page"] = $contacts->has_previus_page() ? $contacts->get_current_page() - 1 : null;
$return["next_page"] = $contacts->has_next_page() ? $contacts->get_current_page() + 1 : null;
$return["last_page"] = null;
$return["data_rows"] = $result->num_rows;
$return["data_fields"] = [];
$return["data"] = [];

foreach ($result->fetch_fields() as $field)
    array_push($return["data_fields"], $field->name);

while($data = $result->fetch_assoc())
    array_push($return["data"], $data);

echo json_encode($return, JSON_UNESCAPED_UNICODE);