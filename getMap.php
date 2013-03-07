<?php
require './system/classMap.class.php';
require './system/functions.php';

$map = new classMap();
var_dump($map->getMap("./system/", './models/', './controllers/'));
$map->saveMapToFile('./cfg/map.txt');

var_dump($map->getMap('./plugins/*/'));
?>
