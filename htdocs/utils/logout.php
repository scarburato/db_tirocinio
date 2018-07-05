<a href="../index.php">Qualcosa non ha funzionato, premere per tornare alla casa</a>
<br>
<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 19/01/18
 * Time: 20.19
 */

require_once "lib.hphp";

$user = new \auth\User();

if($user->get_token() !== NULL)
{
    $google_client->revokeToken($user->get_token());
}

$user->erase();
session_destroy();

header("Location: ../index.php");