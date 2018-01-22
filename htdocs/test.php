<?php
require_once "utils/lib.hphp";
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
if($user["hd"] !== "itispisa.gov.it")
{
    // Disconessione
    $google_client->revokeToken();
    header("Location: index.php?wrong_domain='la mela e stata mangiata'");
    die("Dominio errato! Non si dovrobbe arrivare a questo punto");
}

// TODO Controllare tipo d'utenza nel db
$_SESSION["user"]["type"] = \auth\LEVEL_GOOGLE_STUDENT;
$_SESSION["user"]["id"] = "No db";
$_SESSION["user"]["token"] = $token;

header("Location: index.php");