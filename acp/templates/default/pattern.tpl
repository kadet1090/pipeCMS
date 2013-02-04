<!DOCTYPE html>
<html>
    <head>
        <meta charset="<?php echo $config->info->charset ?>" />
        <link rel="stylesheet" href="<?php echo self::$templateDir ?>/style.css" />
        <title><?php echo self::getTitle() ?></title>
    </head>
    <body>
        <header>
            <hgroup>
                <h1><?php echo $config->info->title ?></h1>
                <h2><?php echo language::get("acp"); ?></h2>
            </hgroup>
            <?php if($currentUser->isLogged): ?>
                <span id="user"><span><?php echo language::get("hello") ?></span>, <?php echo $currentUser->login ?>! [<?php echo language::get("logout") ?>]</span>
            <?php endif; ?>
        </header>
    </body>
</html>