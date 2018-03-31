<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 31/03/18
 * Time: 10.42
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();
$user = new auth\User();
$user->is_authorized(\auth\LEVEL_ALL, \auth\User::UNAUTHORIZED_REDIRECT);

?>

<html lang="en">
<head>
    <?php include ($_SERVER["DOCUMENT_ROOT"]) ."/utils/pages/head.phtml"; ?>
</head>
<body>
<nav class="navbar is-info">
    <div class="navbar-brand">
        <a href="<?= BASE_DIR ?>index.php" class="title navbar-item">
            <?= SITE_NAME ?>
        </a>
    </div>
</nav>
<br>
<section class="container">
    <div class="content">
        <h2>Licenza</h2>
        <h3>Boh License</h3>
        <p class="has-text-justified">Scrivimi</p>
        <h2>Licenze prodotti di terze parti</h2>
        <table>
            <thead>
            <tr>
                <th>Componente</th>
                <th>Licenza</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th>jQuery v3.3.1</th>
                <td>MIT license</td>
                <td><a href="https://jquery.org/license">Licenza</a></td>
            </tr>
            <tr>
                <th>PapaParse v4.3.2</th>
                <td>MIT License</td>
                <td><a href="https://github.com/mholt/PapaParse/blob/master/LICENSE">Licenza</a></td>
            </tr>
            <tr>
                <th>SCEditor v2.1.2</th>
                <td>MIT License</td>
                <td><a href="https://www.sceditor.com/documentation/support-licensing/">Licenza</a></td>
            </tr>
            <tr>
                <th>jQuery MD5 Plugin 1.2.1</th>
                <td>MIT License</td>
                <td><a href="<?= BASE_DIR ?>js/lib/jquery.md5.js">Licenza</a></td>
            </tr>
            <tr>
                <th>jQuery printElement - v2.0.1</th>
                <td>MIT License</td>
                <td><a href="https://github.com/erikzaadi/jQuery.printElement/blob/master/LICENSE-MIT">Licenza</a></td>
            </tr>
            <tr>
                <th>CoinHive Captcha</th>
                <td>?</td>
                <td>?</td>
            </tr>
            <tr>
                <th>Bulma</th>
                <td>MIT License</td>
                <td><a href="https://github.com/jgthms/bulma/blob/master/LICENSE">Licenza</a></td>
            </tr>
            <tr>
                <th>Font Awesome 4.7.0</th>
                <td>Font: SIL OFL 1.1, CSS: MIT License</td>
                <td><a href="https://fontawesome.com/v4.7.0/license/">Licenza</a></td>
            </tr>
            <tr>
                <th>Google APIs Client Library for PHP ^2.0</th>
                <td>Apache License 2.0</td>
                <td><a href="https://github.com/google/google-api-php-client/blob/master/LICENSE">Licenza</a></td>
            </tr>
            </tbody>
        </table>
    </div>
</section>
<?php include ($_SERVER["DOCUMENT_ROOT"]) . "/utils/pages/footer.phtml"; ?>
</body>
</html>