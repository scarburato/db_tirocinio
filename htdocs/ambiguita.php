<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 29/01/18
 * Time: 11.21
 */
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_BOTH, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveStudenteFromDatabase($server)));

$oauth2 = \auth\connect_token_google($google_client, $user->get_token());

if(isset($_GET["tipo"]))
{
    if($_GET["tipo"] == 0)
        $user->set_type(\auth\LEVEL_GOOGLE_STUDENT);
    elseif($_GET["tipo"] == 1)
        $user->set_type(\auth\LEVEL_GOOGLE_TEACHER);

    header("Location: index.php");
    die("Finito!");
}
?>

<html lang="it">
<head>
    <?php include "utils/pages/head.phtml"; ?>
</head>
<body>
    <section class="section">
        <div class="container has-text-centered">
            <div class="columns">
                <div class="column is-4 is-offset-4">
                    <div class="box">
                        <h1 class="title">Ambiguità</h1>
                        <p class="has-text-justified">
                            <?php var_dump($_GET) ?>
                            L'utente risulta essere in ambedue i gruppi: studente e docente.<br>
                            Per continuare selezionare il tipo d'utenza
                        </p>
                        <br>
                        <div>
                            <a class="button is-link is-outlined is-large is-fullwidth" href="ambiguita.php?tipo=0">
                                Studente
                            </a>
                        </div>
                        <br>
                        <div>
                            <a class="button is-link is-outlined is-large is-fullwidth" href="ambiguita.php?tipo=1">
                                Docente
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>