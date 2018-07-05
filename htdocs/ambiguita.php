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

$google_user = new \auth\GoogleConnection($user); $oauth2 = $google_user->getUserProps();

if(isset($_GET["tipo"]))
{
    if($_GET["tipo"] == \auth\LEVEL_GOOGLE_STUDENT)
        $user->set_type(\auth\LEVEL_GOOGLE_STUDENT);
    elseif($_GET["tipo"] == \auth\LEVEL_GOOGLE_TEACHER)
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
        <div class="container is-fluid">
            <div class="columns">
                <div class="column is-4 is-offset-4">
                    <div class="box">
                        <h1 class="title">Ambiguità</h1>
                        <p class="has-text-justified">
                            L'utente <?= sanitize_html($user_info->username) ?> risulta essere in ambedue i gruppi: studente e docente.<br>
                            Per continuare selezionare il tipo d'utenza
                        </p>
                        <br>
                        <div>
                            <a class="button is-link is-outlined is-large is-fullwidth"
                               href="ambiguita.php?tipo=<?= urlencode(\auth\LEVEL_GOOGLE_STUDENT) ?>">
                                Studente
                            </a>
                        </div>
                        <br>
                        <div>
                            <a class="button is-link is-outlined is-large is-fullwidth"
                               href="ambiguita.php?tipo=<?= urlencode(\auth\LEVEL_GOOGLE_TEACHER) ?>">
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