<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 22/03/18
 * Time: 18.08
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$google_user = new \auth\GoogleConnection($user); $oauth2 = $google_user->getUserProps();

// Variabili pagina
$page = "Gestione dei gruppi";
$server = new \mysqli_wrapper\mysqli();
?>
<html lang="it">
<head>
    <?php include ($_SERVER["DOCUMENT_ROOT"]) ."/utils/pages/head.phtml"; ?>
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
            <button id="new_group_toggle" class="button is-fullwidth is-info is-large">
                Crea un nuovo gruppo!
            </button>
            <div id="new_group" hidden>
                <form action="crea_gruppo.php" method="POST">
                    <div class="field">
                        <label class="label">
                            Crea un nuovo gruppo
                        </label>
                        <div class="control">
                            <input class="input" type="text" maxlength="126" placeholder="Nome del nuovo gruppo..." name="name">
                        </div>
                    </div>
                    <div class="field">
                        <div class="control">
                            <textarea class="textarea" maxlength="255" placeholder="Descrizione nuovo gruppo..." name="desc"></textarea>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control">
                            <button type="submit" class="button is-pulled-right is-link">
                                Crea
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <table class="table is-fullwidth">
                <thead>
                <tr>
                    <td style="width: 1rem;"></td>
                    <th>Nome</th>
                    <th>Descrizione</th>
                    <td style="width: 10%" colspan="2"></td>
                </tr>
                </thead>
                <tbody>
                <?php
                $gruppi = $server->prepare("SELECT nome, descrizione FROM Gruppo");
                $gruppi->execute();
                $gruppi->bind_result($nome, $desc);
                while($gruppi->fetch())
                {
                    ?>
                    <tr>
                        <td>
                        <span class="icon">
                            <i class="fa fa-users" aria-hidden="true"></i>
                        </span>
                        </td>
                        <th><?= filter_var($nome, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></th>
                        <td><?= filter_var($desc, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></td>
                        <td>
                            <?php
                            if($nome !== "root")
                            {
                                ?>
                                <a href="gruppo.php?group=<?= filter_var($nome, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?>"
                                   class="button is-warning is-narrow is-fullwidth">
                                    <span class="icon">
                                        <i class="fa fa-gear" aria-hidden="true"></i>
                                    </span>
                                    <span>
                                        Imposta
                                    </span>
                                </a>
                                <?php
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if($nome !== "root")
                            {
                                ?>
                                <a href="elimina_gruppo.php?group=<?= filter_var($nome, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?>"
                                   class="button is-danger is-narrow is-fullwidth">
                                    <span class="icon">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </span>
                                    <span>
                                        Elimina
                                    </span>
                                </a>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
                $gruppi->close();
                ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>

<script src="<?= BASE_DIR ?>js/table/tableSelection.js"></script>
<script src="js/creazione_modifica_ed_eliminazione_dei_gruppi.js"></script>
</body>
</html>
