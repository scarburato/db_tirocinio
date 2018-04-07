<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 02/03/18
 * Time: 12.49
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$oauth2 = \auth\connect_token_google($google_client, $user->get_token());

$info = $server->prepare("SELECT id, indirizzo_posta, D.utente IS NOT NULL AS 'isDocente', S.utente IS NOT NULL AS 'isStudente'
                                  FROM UtenteGoogle
                                  LEFT JOIN Docente D ON UtenteGoogle.id = D.utente
                                  LEFT JOIN Studente S ON UtenteGoogle.id = S.utente
                                WHERE id = ?");
$info->bind_param("i",$_GET["utente"]);
$info->execute(true);
$info->bind_result(
        $id,
        $posta,
        $isDocente,
        $isStudente
);

$info->fetch();
$info->close();

// Variabili pagina
$page = "Informazioni su " . $posta;
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
            <table class="table is-fullwidth">
                <tr>
                    <th>È studente?</th>
                    <td><?= $isStudente ? "SÌ" : "NO" ?></td>
                </tr>
                <tr>
                    <th>È docente?</th>
                    <td><?= $isDocente ? "SÌ" : "NO" ?></td>
                </tr>
            </table>
            <?php
            if($isStudente)
            {
                ?>
                <h2 class="title is-2">Configura Studente</h2>
                <div class="field">
                    <label class="label">Imposta matricola</label>
                </div>
                <div class="field has-addons">
                    <div class="control is-expanded">
                        <input class="input" type="text" maxlength="10" placeholder="matricola...">
                    </div>
                    <div class="control">
                        <button class="button is-link">
                            Salva
                        </button>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Imposta indirizzo</label>
                </div>
                <div class="field has-addons">
                    <div class="control is-expanded">
                        <div class="select is-fullwidth">
                            <select title="indirizzi" id="indirizzo">
                                <option>Lascia vuoto</option>
                                <optgroup label="Indirizzi">
                                    <?php
                                    $indirizzi = $server->prepare("SELECT id, indirizzo FROM Indirizzo");
                                    $indirizzi->execute(true);
                                    $indirizzi->bind_result(
                                            $id, $indirizzo
                                    );
                                    while($indirizzi->fetch())
                                    {
                                        ?>
                                        <option value="<?= $id ?>"><?= sanitize_html($indirizzo) ?></option>
                                        <?php
                                    }
                                    ?>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="control">
                        <button class="button is-link">
                            Salva
                        </button>
                    </div>
                </div>
                <?php
            }
            if($isDocente)
            {
                ?>
                <h2 class="title is-2">Configura Docente</h2>
                <div class="buttons">
                    <?php
                    if(\auth\check_permission($server, "user.groups", false))
                    {
                        ?>
                        <a class="button" href="../permissions/?user=<?= urlencode($posta) ?>">
                            Configura permessi
                        </a>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) ."/utils/pages/footer.phtml"; ?>
</body>
</html>
