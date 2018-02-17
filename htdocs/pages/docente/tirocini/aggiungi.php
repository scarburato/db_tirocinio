<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 12/02/18
 * Time: 19.00
 */
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GOOGLE_TEACHER);
$user = \auth\connect_token_google($google_client, $_SESSION["user"]["token"],$oauth2);

// Variabili pagina
$page = "Creazione tirocinio";
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
            $index_menu = 1;
            include "../menu.php";
            ?>
        </aside>
        <div class="column">
            <form action="" method="post">
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">Studente</label>
                    </div>
                    <div class="field-body">
                        <div class="field has-addons is-normal">
                            <div class="control is-expanded">
                                <input class="input" type="text" required readonly
                                       placeholder="Selezionare studente tirocinante">
                                <input hidden type="number" title="studente" name="studente">
                                <p class="help">
                                    Campo obbligatorio
                                </p>
                            </div>
                            <div class="control">
                                <button type="button" class="button is-info" id="seleziona_ateco_trigger">
                                    <span class="icon">
                                        <i class="fa fa-list-alt" aria-hidden="true"></i>
                                    </span>
                                    <span>
                                        Seleziona...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">Azienda</label>
                    </div>
                    <div class="field-body">
                        <div class="field has-addons is-normal">
                            <div class="control is-expanded">
                                <input class="input" type="text" required readonly
                                       placeholder="Selezionare azienda ospitante">
                                <input hidden type="number" title="studente" name="azienda">
                                <p class="help">
                                    Campo obbligatorio
                                </p>
                            </div>
                            <div class="control">
                                <button type="button" class="button is-info" id="seleziona_ateco_trigger">
                                    <span class="icon">
                                        <i class="fa fa-list-alt" aria-hidden="true"></i>
                                    </span>
                                    <span>
                                        Seleziona...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">Docente</label>
                    </div>
                    <div class="field-body">
                        <div class="field has-addons is-normal">
                            <div class="control is-expanded">
                                <input class="input" type="text" required readonly
                                       placeholder="Selezionare docente referente">
                                <input hidden type="number" title="studente" name="azienda">
                                <p class="help">
                                    Campo obbligatorio
                                </p>
                            </div>
                            <div class="control">
                                <button type="button" class="button is-info" id="seleziona_ateco_trigger">
                                    <span class="icon">
                                        <i class="fa fa-list-alt" aria-hidden="true"></i>
                                    </span>
                                    <span>
                                        Seleziona...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">Tutore</label>
                    </div>
                    <div class="field-body">
                        <div class="field has-addons is-normal">
                            <div class="control is-expanded">
                                <input class="input" type="text" readonly
                                       placeholder="Selezionare tutore aziendale">
                                <input hidden type="number" title="studente" name="azienda">
                            </div>
                            <div class="control">
                                <button type="button" class="button is-info" id="seleziona_ateco_trigger">
                                    <span class="icon">
                                        <i class="fa fa-list-alt" aria-hidden="true"></i>
                                    </span>
                                    <span>
                                        Seleziona...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">Periodo</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <div class="control">
                                <input id="test" class="input" type="date" maxlength="16" name="data_inizio"
                                       placeholder="Inizio">
                            </div>
                        </div>
                        <div class="field">
                            <div class="control">
                                <input class="input" type="date" name="data_fine" placeholder="Fine">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
</body>
</html>