<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 23/01/18
 * Time: 20.03
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$google_user = new \auth\GoogleConnection($user); $oauth2 = $google_user->getUserProps();
$permission = new \auth\PermissionManager($server, $user);

// Variabili pagina
$page = "Cassetta degli strumenti";
?>
<html lang="it">
<head>
    <?php include "../../../utils/pages/head.phtml"; ?>
</head>
<body>
<?php include "../../common/google_navbar.php"; ?>
<br>
<section class="container">
    <div class="columns">
        <aside class="column is-3 is-fullheight">
            <?php
            $index_menu = 8;
            include "../menu.php";
            ?>
        </aside>
        <div class="column">
            <?php
            $print = false;
            if($permission->check("user.google.add"))
            {
                $print = true;
                ?>
                <article class="media box">
                    <figure class="media-left">
                    <span class="icon is-large">
                        <i class="fa fa-users fa-2x" aria-hidden="true"></i>
                    </span>
                    </figure>
                    <div class="media-content">
                        <div class="content">
                            <h1>Gestione Utenze scolastiche</h1>
                            <a class="button is-link is-pulled-right" href="users">
                                Configura
                            </a>
                        </div>
                    </div>
                </article>
                <?php
            }
            if($permission->check('user.factory.add') || $permission->check("factory.contacts.create"))
            {
                $print = true;
                ?>
                <article class="media box">
                    <figure class="media-left">
                    <span class="icon is-large">
                        <i class="fa fa-building fa-2x" aria-hidden="true"></i>
                    </span>
                    </figure>
                    <div class="media-content">
                        <div class="content">
                            <h1>Gestione Aziende</h1>
                            <a class="button is-link is-pulled-right" href="./aziende">
                                Configura
                            </a>
                        </div>
                    </div>
                </article>
                <?php
            }

            if($permission->check('control.forgive'))
            {
                $print = true;
                ?>
                <article class="media box">
                    <figure class="media-left">
                        <span class="icon is-large">
                            <i class="fa fa-plug fa-2x" aria-hidden="true"></i>
                        </span>
                    </figure>
                    <div class="media-content">
                        <div class="content">
                            <h1>Controllo degli accessi</h1>
                            <a class="button is-link is-pulled-right" href="./traffico">
                                Configura
                            </a>
                        </div>
                    </div>
                </article>
                <?php
            }
            $gruppi = $permission->check("control.groups");
            $assegnazione_gruppi = $permission->check('user.groups');

            if($assegnazione_gruppi || $gruppi)
            {
                $print = true;
                ?>
                <article class="media box">
                    <figure class="media-left">
                    <span class="icon is-large">
                        <i class="fa fa-legal fa-2x" aria-hidden="true"></i>
                    </span>
                    </figure>
                    <div class="media-content">
                        <div class="content">
                            <h1>Gestione permessi docenti</h1>
                            <div class="field is-grouped is-grouped-right">
                                <?php
                                if($gruppi)
                                {
                                    ?>
                                    <p class="control">
                                        <a href="permissions/gruppi.php" class="button is-link">
                                            Configura Gruppi
                                        </a>
                                    </p>
                                    <?php
                                }

                                if($assegnazione_gruppi)
                                {
                                    ?>
                                    <p class="control">
                                        <a href="permissions" class="button is-link">
                                            Configura Utenti
                                        </a>
                                    </p>
                                    <?php
                                }?>
                            </div>
                        </div>
                    </div>
                </article>
                <?php
            }

            if($permission->check('control.throw'))
			{
				?>
				<article class="media box">
					<figure class="media-left">
                        <span class="icon is-large">
                            <i class="fa fa-bug fa-2x" aria-hidden="true"></i>
                        </span>
					</figure>
					<div class="media-content">
						<div class="content">
							<h1>Simula un errore negli script</h1>
							<a class="button is-link is-pulled-right" href="./lancia.php">
								Lancia un ecceziùne!
							</a>
						</div>
					</div>
				</article>
				<?php
			}

            if(!$print)
            {
                ?>
                <div class="notification is-danger">
                    Sembra che non si possegga alcun diritto in questa sezione.<br>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>
</body>
</html>