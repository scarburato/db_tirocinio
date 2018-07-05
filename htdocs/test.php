<?php
require_once "utils/lib.hphp";
require_once "fakeService/init.php";
require_once "utils/auth.hphp";

/* TODO rinominare in auth.php
 * (È necessario modificare impostazione da Google :P)
 */

// Ottenere il token d'accesso
$google_client->fetchAccessTokenWithAuthCode($_GET["code"]);
$token = $google_client->getAccessToken();

$google_client->setAccessToken($token);

$oauth2 = new \Google_Service_Oauth2($google_client);
$user = $oauth2->userinfo->get();

// Controllo del domino
if($user->hd !== TRUSTED_DOMAIN)
{
    // Disconessione
    $google_client->revokeToken();
    redirect("index.php", [
        "wrong_domain" => true
    ]);
}

// Variabili
$id = null;

// Aggiunta al servente¡
$server = new \mysqli_wrapper\mysqli();

// Controllo tipologia d'utenza
build($google_client_2);
$servizi = new Google_Service_Directory($google_client_2);

$info = \auth\aggiungi_utente_database($server, $user, $servizi);

$db_user = new \auth\User();
$db_user->set_database_id($info["id"]);
$db_user->set_token($token);
$db_user->set_type($info["type"]);

redirect("ambiguita.php", [
    "tipo" => $info["type"]
]);