<?php
abstract class plugin implements pluginInterface
{
    public static $directory;

    public function __construct($dir)
    {
        self::$directory = $dir;
    }
}