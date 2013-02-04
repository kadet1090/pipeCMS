<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of xml
 *
 * @author admin
 */
class xml extends dataFile implements dataFileInterface
{
    public function load()
    {
	if(!isset($this->_filePath)) throw new frameworkException('Path to file is not set!', 1101);
	if(!file_exists($this->_filePath)) throw new frameworkException('File not exists please chech path', 1102);
	$this->_data = simplexml_load_file($this->_filePath);
    }
    
    public function save() 
    {
	if(!isset($this->_filePath)) throw new frameworkException('Path to file is not set!', 1101);
	if(!isset($this->_data)) throw new frameworkException('You must set data first!', 1103);
	file_put_contents($filename, $this->_data->asXML());
    }
    
    public function count()
    {
	return count($this->_data, COUNT_RECURSIVE);
    }
}
?>
