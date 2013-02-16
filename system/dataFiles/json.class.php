<?php
class json extends dataFile implements dataFileInterface
{
    public function load()
    {
        if(!isset($this->_filePath)) throw new frameworkException('Path to file is not set!', 1101);
        if(!file_exists($this->_filePath)) throw new framerowkException('File not exists please chech path', 1102);
        $this->_data = ArrayToObject(json_decode(file_get_contents($this->_filePath)));
    }
    
    public function save() 
    {
        if(!isset($this->_filePath)) throw new frameworkException('Path to file is not set!', 1101);
        if(!isset($this->_data)) throw new frameworkException('You must set data first!', 1103);
        file_put_contents($this->_filePath, json_encode(ObjectToArray($this->_data)));
    }
    
    public function count()
    {
        return count($this->_data, COUNT_RECURSIVE);
    }
}
?>
