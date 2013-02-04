<?php
require '../system/classMap.class.php';
require '../system/functions.php';

$map = classMap::getInstance();
$map->getMap("./", "../system/", '../models/');
$map->saveMapToFile('../cfg/acp-map.txt');
?>
