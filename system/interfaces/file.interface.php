<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Admin
 */
interface fileInterface
{
    public function load($fileName, $cFileType);
    public function read($numberOfChars);
    public function write($text);
}
?>
