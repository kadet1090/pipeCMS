<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author admin
 */
interface dataFileInterface
{   
    public function load();
    public function  __get($name);
    public function  __set($name, $mValue);
    public function count();
    public function  __isset($name);
    public function save();
}
?>
