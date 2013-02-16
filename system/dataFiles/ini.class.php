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
class ini extends dataFile implements dataFileInterface
{
    public function load()
    {
        if(!isset($this->_filePath)) throw new frameworkException('Path to file is not set!', 1101);
        if(!file_exists($this->_filePath)) throw new framerowkException('File not exists please chech path', 1102);
        $this->_data = ArrayToObject(parse_ini_file($this->_filePath, true));
    }
    
    public function save() 
    {
        if(!isset($this->_filePath)) throw new frameworkException('Path to file is not set!', 1101);
        if(!isset($this->_data)) throw new frameworkException('You must set data first!', 1103);
        
        $res = array();
        foreach(ObjectToArray($this->_data) as $key => $mVal)
        {
            if(is_array($mVal))
            {
                $res[] = '['.$key.']';
                foreach($mVal as $skey => $mVal) 
                    $res[] = $skey.' = '.(is_numeric($mVal) ? $mVal : '"'.$mVal.'"');
            }
            else $res[] = $key.' = '.(is_numeric($mVal) ? $mVal : '"'.$mVal.'"');
        }
        file_put_contents($this->_filePath, implode('\n', $res));
    }
    
    public function count()
    {
        return count($this->_data, COUNT_RECURSIVE);
    }
}
?>
