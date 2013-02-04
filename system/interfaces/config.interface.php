<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Admin
 */
interface config
{
    public function load($file, $cType = null);
    public function setType($cType);
}
?>
