<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 04/02/18
 * Time: 13.28
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) ."/utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GOOGLE_TEACHER);
$oauth2 = \auth\connect_token_google($google_client, $_SESSION["user"]["token"]);
$user = \auth\get_user_info($oauth2);

// Variabili pagina
$page = "Gestione Aziende - Riepilogo ultimo inserimento";

// Interrogazione db
$server = new \mysqli_wrapper\mysqli();

$azienda = $server->prepare(
        "SELECT Azienda.id, nominativo, IVA, codiceFiscale, cod2007, gestione, C.descrizione FROM Azienda 
                INNER JOIN CodiceAteco ON Azienda.ateco = CodiceAteco.id
                INNER JOIN Classificazioni C ON Azienda.classificazione = C.id
                WHERE Azienda.id = ?");
$azienda->bind_param(
        "i",
        $_GET["id"]
);
$azienda->execute(true);

$azienda->bind_result(
        $id,
        $nome,
        $iva,
        $cf,
        $ateco,
        $gestione,
        $classificazione
);
$azienda->store_result();
$azienda->fetch();
$azienda->close();
?>
<html lang="it" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
<head>
    <?php include ($_SERVER["DOCUMENT_ROOT"]) ."/utils/pages/head.phtml"; ?>
    <script src="<?= BASE_DIR ?>js/jquery.printElement.min.js"></script>
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
            <article class="message is-info">
                <div class="message-body">
                    <p class="has-text-justified">
                        Questo è il riepilogo contente le informazioni relative all'azienda appena inserita.<br>
                        Sarà anche l'unico momento in cui sarà possibile osservare la parola d'ordine scritta in chiaro:
                        <strong>sulla macchina remota la parola d'ordine è stata eliminata con il completamento del processo di creazione di questa pagina. Ricaricare quest'ultima causerà la perdita dalla parola d'ordine!<br></strong>
                        Stampare questo documento e consegnarlo all'interessato.<br>
                        All'interessato sarà richiesto di cambiare parola d'ordine appena effettuato il primo accesso.<br>
                        <strong>Rammentarsi che in caso di smarrimento di questo documento solo un docente con privilegi sufficientemente elevati potrà modificare manuale la parola d'ordine.</strong>
                    </p>
                </div>
            </article>
            <div id="print_area">

                <h3 class="title is-3">
                    Credenziali primo accesso
                </h3>
                <table class="table is-fullwidth is-bordered">
                    <tr>
                        <th style="width: 45%;">Codice univco d'accesso</th>
                        <td><?= $id ?></td>
                    </tr>
                    <tr>
                        <th>Parola d'ordine impostata</th>
                        <td>
                            <?php
                            if(isset($_SESSION["last_passwd"]))
                            {
                                echo $_SESSION["last_passwd"]["passwd"];
                                unset($_SESSION["last_passwd"]);
                            }
                            else
                            {
                                ?>
                                <p class="help is-danger"><i>Non pervenuta. È stata ricaricata la pagina?</i></p>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                </table>

                <h3 class="title is-3">Dati anagrafici</h3>
                <table class="table is-fullwidth is-bordered">
                    <tr>
                        <th style="width: 45%">Nominativo</th>
                        <td><?= $nome ?></td>
                    </tr>
                    <tr>
                        <th>Partita IVA</th>
                        <td><?= $iva ?></td>
                    </tr>
                    <tr>
                        <th>Codice Fiscale</th>
                        <td><?= $cf ?></td>
                    </tr>
                    <tr>
                        <th>Tipo Gestione</th>
                        <td><?= $gestione ?></td>
                    </tr>
                    <tr>
                        <th>Tipo Classificazione</th>
                        <td><?= $classificazione ?></td>
                    </tr>
                    <tr>
                        <th>Codice ATECO 2007</th>
                        <td><?= $ateco ?></td>
                    </tr>
                </table>

                <h3 class="title is-3 gao" hidden>Sedi</h3>
                <table class="table is-fullwidth is-bordered is-striped gao" hidden>
                    <thead>
                    <tr>
                        <th>Sede</th>
                        <th>Indirizzo</th>
                        <th title="Codice d'Avviamento Postale">CAP</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sedi = $server->prepare("SELECT nomeSede, CONCAT(indirizzo, ',', numCivico, ',', comune, ',', provincia, ',' ,stato) AS 'indirizzo', CAP FROM Sede WHERE azienda = ?");
                    $sedi->bind_param(
                            "i",
                            $id
                    );
                    $sedi->execute(true);
                    $sedi->bind_result($nome, $indirizzo, $cap);
                    while ($sedi->fetch())
                    {
                        ?>
                        <tr>
                            <td><?= $nome ?></td>
                            <td><?= $indirizzo ?></td>
                            <td><?= $cap ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="is-fullwidth">
                <a class="button is-info is-large is-fullwidth" id="stampalo">
                    <span class="icon">
                        <i class="fa fa-print"></i>
                    </span>
                    <span>
                        Stampa
                    </span>
                </a>
                <label class="checkbox">
                    <input type="checkbox" id="stampare_sedi">
                    Spuntare la casella di controllo per stampare anche l'elenco delle sedi inserite.
                </label>
            </div>
        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) ."/utils/pages/footer.phtml"; ?>
<script src="js/stampa.js"></script>
</body>
</html>