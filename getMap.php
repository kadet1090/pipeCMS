<?php
require './system/classMap.class.php';
require './system/functions.php';

$map = classMap::getInstance();
$map->getMap("./system/", './models/', './controllers/');
$map->saveMapToFile('./cfg/map.txt');
?>
