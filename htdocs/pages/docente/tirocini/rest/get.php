<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 17/02/18
 * Time: 8.27
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

\auth\check_and_redirect(\auth\LEVEL_GOOGLE_TEACHER);
$user = \auth\connect_token_google($google_client, $_SESSION["user"]["token"],$oauth2);
