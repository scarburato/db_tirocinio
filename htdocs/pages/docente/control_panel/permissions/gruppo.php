<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 22/03/18
 * Time: 18.08
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

if($_GET["group"] === "root")
    redirect("../");

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$oauth2 = \auth\connect_token_google($google_client, $user->get_token());

// Variabili pagina
$page = "Gestione del gruppo `{$_GET['group']}`";
$server = new \mysqli_wrapper\mysqli();

// Controllo se il gruppo esiste!
$gruppo = $server->prepare("SELECT nome, descrizione FROM Gruppo WHERE nome = ?");
$gruppo->bind_param(
        "s",
        $_GET["group"]
);

$gruppo->execute(true);
$gruppo->bind_result($current_nome, $current_descrizione);
if(!$gruppo->fetch())
    redirect("../");

$gruppo->close();
?>
<html lang="it">
<head>
    <?php include ($_SERVER["DOCUMENT_ROOT"]) ."/utils/pages/head.phtml"; ?>
    <script>
        const CURRENT_GROUP = '<?= sanitize_html($current_nome) ?>';
    </script>
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
            <div class="box is-fullwidth">
                <h1 class="title is-1 has-text-centered"><?= sanitize_html($current_nome) ?></h1>
                <p class="has-text-justified"><?= sanitize_html($current_descrizione) ?></p>
            </div>
            <div class="field">
                <p class="control">
                    <button id="commit" class="button is-large is-primary is-fullwidth">
                            <span class="icon">
                                <i class="fa fa-floppy-o" aria-hidden="true"></i>
                            </span>
                        <span>
                                Salva
                            </span>
                    </button>
                </p>
            </div>
            <div class="columns is-fullheight">
                <div class="column is-5 is-fullheight is-paddingless">
                    <h3 class="title is-3 has-text-centered">Applicati</h3>
                    <div class="box is-paddingless overflow" style="height: 30em">
                        <table class="table is-narrow">
                            <tbody id="applicati">
                            <?php
                            $permessi_attuali = $server->prepare(
                                "SELECT nome, descrizione 
                                          FROM PermessiGruppo
                                          INNER JOIN Privilegio P ON PermessiGruppo.privilegio = P.nome
                                        WHERE gruppo = ?"
                            );
                            $permessi_attuali->bind_param(
                                "s",
                                $current_nome
                            );

                            $permessi_attuali->execute(true);
                            $permessi_attuali->bind_result($nome, $descrizione);
                            while($permessi_attuali->fetch())
                            {
                                ?>
                                <tr data-id="<?= sanitize_html($nome) ?>">
                                    <th style="width: 25%"><?= str_replace(".", ".<wbr>", sanitize_html($nome)) ?></th>
                                    <td><p class="has-text-justified"><?= sanitize_html($descrizione) ?></p></td>
                                    <td style="width: 20%">
                                        <a tabindex="">Seleziona</a>
                                    </td>
                                </tr>
                                <?php
                            }
                            $permessi_attuali->close();
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="column is-2">
                    <div class="is-hidden-mobile" style="height: 3em"></div>
                    <div class="field">
                        <div class="control">
                            <button class="button is-fullwidth" id="rimuovi">
                                    <span class="icon">
                                        <i class="fa fa-arrow-right is-hidden-mobile" aria-hidden="true"></i>
                                        <i class="fa fa-arrow-down  is-hidden-tablet " aria-hidden="true"></i>
                                    </span>
                            </button>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control">
                            <button class="button is-fullwidth" id="aggiungi">
                                    <span class="icon">
                                        <i class="fa fa-arrow-left is-hidden-mobile" aria-hidden="true"></i>
                                        <i class="fa fa-arrow-up   is-hidden-tablet" aria-hidden="true"></i>
                                    </span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="column is-5 is-fullheight is-paddingless">
                    <h3 class="title is-3 has-text-centered">Disponibili</h3>
                    <div class="box is-paddingless overflow" style="height: 30em">
                        <table class="table is-fullwidth">
                            <tbody id="privilegi">
                            <?php
                            $permessi_disponibili = $server->prepare(
                                "SELECT nome, descrizione FROM Privilegio P WHERE NOT EXISTS(SELECT privilegio FROM PermessiGruppo WHERE gruppo = ? AND privilegio = P.nome)"
                            );
                            $permessi_disponibili->bind_param(
                                    "s",
                                    $current_nome
                            );

                            $permessi_disponibili->execute(true);
                            $permessi_disponibili->bind_result($nome, $descrizione);
                            while($permessi_disponibili->fetch())
                            {
                                ?>
                                <tr data-id="<?= sanitize_html($nome) ?>">
                                    <th style="width: 25%"><?= str_replace(".", ".<wbr>", sanitize_html($nome)) ?></th>
                                    <td><p class="has-text-justified"><?= sanitize_html($descrizione) ?></p></td>
                                    <td style="width: 20%">
                                        <a tabindex="">Seleziona</a>
                                    </td>
                                </tr>
                                <?php
                            }
                            $permessi_disponibili->close();
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>

<script src="<?= BASE_DIR ?>js/tableSelection.js"></script>
<script src="<?= BASE_DIR ?>js/tableChooser.js"></script>

<script src="js/impostazione_privilegi_dei_gruppi.js"></script>
</body>
</html>
