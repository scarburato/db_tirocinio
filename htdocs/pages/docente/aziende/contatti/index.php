<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 05/04/18
 * Time: 10.25
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$oauth2 = \auth\connect_token_google($google_client, $user->get_token());

$azienda = $server->prepare("SELECT nominativo FROM Azienda WHERE id = ?");
$azienda->bind_param(
    "i",
    $_GET["azienda"]
);
$azienda->bind_result($nome);

$azienda->execute(true);
if(!($azienda->fetch()))
    redirect("../../");

// Variabili pagina
$page = "In contatto con " . sanitize_html($nome);

?>
<html lang="it">
<head>
    <?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/head.phtml"; ?>
</head>
<body>
<?php include "../../../common/google_navbar.php"; ?>
<br>
<section class="container">
    <div class="columns">
        <aside class="column is-3 is-fullheight">
            <?php
            $index_menu = 7;
            include "../../menu.php";
            ?>
        </aside>
        <div class="column">
        </div>
    </div>
</section>
</body>
</html>