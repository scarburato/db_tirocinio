<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 26/02/18
 * Time: 11.05
 */
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();
$user = new auth\User();
$user->is_authorized(\auth\LEVEL_FACTORY, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveAziendaFromDatabase($server)));


$page = "Creazione Contatto"
?>
<html lang="it">
<head>
    <?php include ($_SERVER["DOCUMENT_ROOT"]) ."/utils/pages/head.phtml"; ?>
</head>
<body>
<?php include "../../common/google_navbar.php"; ?>
<br>
<section class="container">
    <div class="columns" style="">
        <aside class="column is-3 is-fullheight">
            <?php
            $index_menu = 1;
            include "../menu.php";
            ?>
        </aside>
        <div class="column">
            <form action="aggiungi_db.php" method="post" id="main_form">
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">Dati Anagrafici</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <input name="nome" class="input" type="text" required
                                   placeholder="Nome" maxlength="64">
                            <p class="help">
                                Campo obbligatorio
                            </p>
                        </div>
                        <div class="field">
                            <div class="field">
                                <input name="cognome" class="input" type="text" required
                                       placeholder="Cognome" maxlength="64">
                                <p class="help">
                                    Campo obbligatorio
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">Recapiti</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <input name="posta" class="input" type="email"
                                   placeholder="Indirizzo di posta elettronica" maxlength="64">
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal"></div>
                    <div class="field-body">
                        <div class="field">
                            <input name="tel" class="input" type="tel"
                                   placeholder="Numero di telefono" maxlength="35">
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal"></div>
                    <div class="field-body">
                        <div class="field">
                            <input name="fax" class="input" type="tel"
                                   placeholder="Numero di telefono telefax" maxlength="35">
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">Qualifica</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <input name="qualifica" class="input" type="text"
                                   placeholder="Qualifica" maxlength="64">
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">Ruolo aziendale</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <textarea name="ruolo" class="textarea"
                                   placeholder="Descrivere brevemente il proprio ruolo aziendale" maxlength="65535"></textarea>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <button class="button is-large is-primary is-fullwidth" type="submit">
                        Registra
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>

<script src="js/validate_contact.js"></script>
</body>
</html>

