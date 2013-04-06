<?php
abstract class plugin implements pluginInterface
{
    public $directory;

    public function __construct($dir)
    {
        $this->directory = $dir;
    }
}