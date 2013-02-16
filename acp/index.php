<?php
    define("DEBUG_MODE", false);

    $startTime = microtime(true); // Czas wystartowania skryptu (potrzebne aby sprawdzić czas wykonywania skryptu)
    date_default_timezone_set('Europe/Warsaw');
    session_start();
    //--------------------------------------includowanie-----------------------------------//
    require '../system/functions.php';                
    require '../system/classMap.class.php';
    require '../system/autoloader.class.php';
    //-------------------------------------------------------------------------------------//
    $map = classMap::getInstance();
    $map->loadMapFromFile('../cfg'.DIRECTORY_SEPARATOR.'acp-map.txt');
    //-------------------------------------------------------------------------------------//
    $loader = new autoloader('./', array('usePrefix' => false, 'usePostfix' => false), $map);
    $loader->ragister();
    //-------------------------------------------------------------------------------------//
    frontController::$configDir = "../cfg/";
    $frontController = new frontController();
    $frontController->work($startTime);
?>