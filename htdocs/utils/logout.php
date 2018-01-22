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
require_once "auth.hphp";

if($_SESSION["user"]["token"] !== NULL)
{
    $google_client->revokeToken($_SESSION["user"]["token"]);
}

\auth\log_out();

header("Location: ../index.php");