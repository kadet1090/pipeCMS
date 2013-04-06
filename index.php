<?php
    if(isset($_GET['debug'])) {
        define("DEBUG_MODE", true);
        ini_set("display_errors", "On");
    }
    else
        define("DEBUG_MODE", false);

    $startTime = microtime(true); // Czas wystartowania skryptu (potrzebne aby sprawdzić czas wykonywania skryptu)
    date_default_timezone_set('Europe/Warsaw');
    session_start();
    //--------------------------------------includowanie-----------------------------------//
    require './system/functions.php';                
    require './system/classMap.class.php';
    require './system/autoloader.class.php';
    //-------------------------------------------------------------------------------------//
    $map = new classMap();
    $map->loadMapFromFile('cfg'.DIRECTORY_SEPARATOR.'map.txt');
    //-------------------------------------------------------------------------------------//
    $loader = new autoloader('./', array('usePrefix' => false, 'usePostfix' => false), $map);
    $loader->ragister();
    //-------------------------------------------------------------------------------------//  
    try {
        $frontController = new frontController();
        $frontController->work($startTime);
    } catch (Exception $exception) {
        $view = new HTMLview('fatal-exception.tpl');
        $view->exception = $exception;

        echo $view;
    }
?>