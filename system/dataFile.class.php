<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of dataFile
 *
 * @author admin
 */
abstract class dataFile
{    
    protected $_filePath;
    protected $_data;
    
    public function __get($name)
    {
	return (isset($this->_data->$name) ? $this->_data->$name : null);
    }
    
    public function __set($name, $mValue)
    {
	$this->_data->$name = $mValue;
    }
    
    public function __isset($name)
    {
	return isset($this->_data->$name);
    }
    
    public function __construct($filePath)
    {
	$this->_filePath = $filePath;
    }
    
    public function load() {}
    public function save() {}
    public function count() {}
}

?>
