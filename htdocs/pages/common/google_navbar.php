<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 20/01/18
 * Time: 20.01
 */

if(false)
{
    ?>
    <pre
            style="height: 15vh; overflow-y: scroll"
            class="has-text-justified"><?= json_encode($_SESSION, JSON_PRETTY_PRINT) ?></pre>
    <?php
}
?>
<nav class="navbar is-info">
    <div class="navbar-brand">
        <a href="<?= BASE_DIR ?>index.php" class="title navbar-item">
            <?= SITE_NAME ?>
        </a>
    </div>

    <div class="navbar-end">
        <div class="navbar-item has-dropdown is-hoverable">
            <a class="navbar-link">
                <?= $user_info->username ?>
            </a>

            <div class="navbar-dropdown">
                <div class="navbar-item">
                    <div class="media">
                        <div class="media-left">
                            <figure class="image is-48x48">
                                <img style="max-height: none" alt="" src="<?= $user_info["picture"] ?>">
                            </figure>
                        </div>
                        <p class="media-content title is-4 is-capitalized">
                            <?= $user_info->nominative ?>
                        </p>
                    </div>
                </div>
                <div class="navbar-item is-pulled-right">
                    <a class="button" href="<?= BASE_DIR ?>utils/logout.php">
                        <span>Esci</span>
                        <span class="icon">
                            <i class="fa fa-sign-out" aria-hidden="true"></i>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
