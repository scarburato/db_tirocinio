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

$google_user = new \auth\GoogleConnection($user); $oauth2 = $google_user->getUserProps();

$info = $server->prepare("SELECT id, indirizzo_posta, nome, cognome,S.matricola, S.indirizzo, D.utente IS NOT NULL AS 'isDocente', S.utente IS NOT NULL AS 'isStudente'
                                  FROM UtenteGoogle U
                                  LEFT JOIN Docente D ON U.id = D.utente
                                  LEFT JOIN Studente S ON U.id = S.utente
                                WHERE id = ?");
$info->bind_param("i",$_GET["utente"]);
$info->execute();
$info->bind_result(
        $id,
        $posta,
        $nome,
        $cognome,
        $studente_matricola,
        $studente_indirizzo,
        $isDocente,
        $isStudente
);

if($info->fetch() !== true)
	throw new RuntimeException("L'utente cercato non esiste!");

$info->close();

$permissions = new \auth\PermissionManager($server, $user);

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
			<h4 class="title is-4"><?= sanitize_html($nome . " " .$cognome) ?></h4>
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
				<form action="setMatricola.php" method="get">
					<input name="studente" type="hidden" value="<?= $id ?>">
					<div class="field has-addons">
						<div class="control is-expanded">
							<input name="matricola" class="input" type="text" maxlength="10" value="<?= sanitize_html($studente_matricola) ?>" placeholder="matricola...">
						</div>
						<div class="control">
							<button type="submit" class="button is-link">
								Salva
							</button>
						</div>
					</div>
				</form>
				<p class="help">La matricola ha lunghezza massima di 10 caratteri</p>
				<div class="field">
                    <label class="label">Imposta indirizzo</label>
                </div>
				<form action="setIndirizzo.php" method="get">
					<div class="field has-addons">
						<div class="control is-expanded">
							<div class="select is-fullwidth">
								<input name="studente" type="hidden" value="<?= $id ?>">
								<select name="indirizzo" title="indirizzi" id="indirizzo">
									<option value="">Lascia vuoto</option>
									<optgroup label="Indirizzi">
										<?php
										$indirizzi = $server->prepare("SELECT id, indirizzo FROM Indirizzo");
										$indirizzi->execute();
										$indirizzi->bind_result(
												$id, $indirizzo
										);
										while($indirizzi->fetch())
										{
											?>
											<option value="<?= $id ?>" <?= $studente_indirizzo === $id ? "selected" : "" ?>><?= sanitize_html($indirizzo) ?></option>
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
				</form>
                <?php
            }
            if($isDocente)
            {
                ?>
                <h2 class="title is-2">Configura Docente</h2>
                <div class="buttons">
                    <?php
                    if($permissions->check("user.groups", \auth\PermissionManager::UNAUTHORIZED_RETURN_FALSE))
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
