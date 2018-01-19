<?php
require_once "utils/lib.hphp";
require_once "utils/auth.hphp";
require_once '../vendor/autoload.php';

/* TODO rinominare in auth.php
 * (È necessario modificare impostazione da Google :P)
 */

error_reporting(E_ALL & ~E_NOTICE);
$google_client = new Google_Client();
$google_client->setAuthConfig("../client_secret_142180740412-f5mtm2geteu9jn5jgi5b9l9uhva5b40b.apps.googleusercontent.com.json");
$google_client->setRedirectUri("http://localhost:63342/DB_Tirocini/test.php");
$google_client->addScope("https://www.googleapis.com/auth/userinfo.email");
$google_client->addScope("https://www.googleapis.com/auth/userinfo.profile");
$oauth2 = new \Google_Service_Oauth2($google_client);

// if issett $_GET code
echo $_GET["code"];
// TODO modifcare sessione
var_dump( $google_client->fetchAccessTokenWithAuthCode($_GET["code"]));

$token = $google_client->getAccessToken();
print_r($token);

$google_client->setAccessToken($token);

$user = $oauth2->userinfo->get();

print_r($user);