<?php
abstract class plugin implements pluginInterface
{
    public $directory;

    public function __construct()
    {
        $this->directory = __DIR__;
    }
}