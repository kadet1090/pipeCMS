<!DOCTYPE html>
<html lang="<?php echo language::getLang(); ?>">
    <head>
        <meta name="robots" content="<?php echo self::$robots; ?>" />
        <link type="text/css" rel="stylesheet" href="<?php echo self::$templateDir ?>/error.css" />
        <title><?php echo self::getTitle() ?></title>
    </head>
    <body>
        <header>
            <h1>PipeCMS</h1>
        </header>
        <article>
            <h1>Fatal Error</h1>
            <div class="content">
                <?php echo $exception->getMessage(); ?>
            </div>
            <table class="data-table">
                <tr>
                    <th></th>
                    <th>File</th>
                    <th>Line</th>
                    <th>Function</th>
                </tr>
                <?php foreach($exception->getTrace() as $no => $trace): ?>
                <tr>
                    <td><?php echo $no + 1; ?></td>
                    <td><?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '.', str_replace(DIRECTORY_SEPARATOR, '/', $trace["file"])); ?></td>
                    <td><?php echo $trace["line"]; ?></td>
                    <td><?php echo (isset($trace["class"]) ? $trace["class"].$trace["type"] : '').$trace['function'].'('.join2(', ', $trace['args']).')'; ?></td>
                </tr>
                <?php endforeach;?>
            </table>
            <pre>
UA: <?php echo $_SERVER["HTTP_USER_AGENT"]; ?> 
HR: <?php echo $_SERVER["HTTP_REFERER"]; ?> 
IP: <?php echo $_SERVER["REMOTE_ADDR"]; ?>
            </pre>
        </article>
    </body>
</html>
