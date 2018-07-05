<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 21/05/18
 * Time: 18.29
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));
$permissions = new \auth\PermissionManager($server, $user);

$permissions->check("factory.contacts.create", \auth\PermissionManager::UNAUTHORIZED_REDIRECT);

$google_user = new \auth\GoogleConnection($user); $oauth2 = $google_user->getUserProps();

$info = $server->prepare("SELECT nominativo 
                                  FROM Azienda 
                                  INNER JOIN Classificazioni C ON Azienda.classificazione = C.id
                                  INNER JOIN CodiceAteco C2 ON Azienda.ateco = C2.id
                                  WHERE Azienda.id = ?");

$info->bind_param("i", $_GET["id"]);
$info->execute();
$info->bind_result($nome);
$info->store_result();
if($info->fetch() !== true)
	throw new RuntimeException("Azienda non esistente!", -1);
?>

<html lang="it">
<head>
    <?php include ($_SERVER["DOCUMENT_ROOT"]) .  "/utils/pages/head.phtml"; ?>
</head>
<body>
<?php include "../../../common/google_navbar.php"; ?>
<br>
<section class="container">
    <div class="columns">
        <aside class="column is-3 is-fullheight">
            <?php
            $index_menu = 8;
            include "../../menu.php";
            ?>
        </aside>
        <div class="column">
			<h4 class="title is-4">Creazione di un contatto</h4>
			<h4 class="subtitle is-4"><?= sanitize_html($nome) ?></h4>
			<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/pages/common/mysql_error.php"; ?>
			<form action="./aggiungi_contatto_db.php" class="valida_iso" method="post" id="main_form">
				<input name="id" type="hidden" value="<?= $_GET["id"] ?>" hidden>
				<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/pages/common/create_contact.php"; ?>
			</form>
		</div>
	</div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) .  "/utils/pages/footer.phtml"; ?>
</body>
</html>
