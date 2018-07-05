<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 17/03/18
 * Time: 17.45
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$google_user = new \auth\GoogleConnection($user); $oauth2 = $google_user->getUserProps();

// Variabili pagina
$page = "Lista aziende";
?>

<html lang="it">
<head>
    <?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/head.phtml"; ?>
</head>
<body>
<?php include "../../common/google_navbar.php"; ?>
<br>
<section class="container">
    <div class="columns">
        <aside class="column is-3 is-fullheight">
            <?php
            $index_menu = 6;
            include "../menu.php";
            ?>
        </aside>
        <div class="column">
            <div class="box">
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">Filtra nome</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <div class="control is-expanded">
                                <input class="input" id="filter_text" type="text" placeholder="Parola chiave">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">Indirizzo</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <div class="control is-expanded">
                                <div class="select is-fullwidth">
                                    <select title="docente" id="filter_values_ind">
                                        <option value="all" selected>Tutte le specializzazioni</option>
                                        <optgroup label="Indirizzi di studio">
                                            <?php
                                            $indirizzi = $server->prepare("SELECT id, indirizzo FROM Indirizzo");
                                            $indirizzi->execute();
                                            $indirizzi->bind_result($id, $nome);
                                            while($indirizzi->fetch())
                                            {
                                                ?>
                                                <option value="<?= $id ?>"><?= sanitize_html($nome)?></option>
                                                <?php
                                            }
                                            $indirizzi->close();
                                            ?>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="control has-text-right" >
                    <button class="button is-info" id="filtralo">
                        <span class="icon">
                            <i class="fa fa-filter" aria-hidden="true"></i>
                        </span>
                        <span>Filtra</span>
                    </button>
                </p>
            </div>
            <div id="dynamic_factories_loading" class="has-text-centered">
                <p>
                    <span class="icon">
                        <i class="fa fa-circle-o-notch fa-spin fa-fw"></i>
                    </span>
                    <span>
                        Interrogazione in corso...
                    </span>
                </p>
            </div>
            <div id="dynamic_factories">

            </div>
        </div>
    </div>
</section>

<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>

<script src="<?= BASE_DIR ?>js/DynamicPagination.js"></script>

<script src="js/din_list.js"></script>
</body>