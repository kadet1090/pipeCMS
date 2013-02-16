<?php

abstract class view
{
    protected $_data = array();
    
    static public $templateDir;
    protected $_templateFileName;
    
    static $robots;
    static protected $_title;
    
    abstract public function render();
    
    public function setTemplateFileName($tmpFN)
    {
        $this->_templateFileName = $tmpFN;
        return $this;
    }
    
    public function __construct($tmpFN = '') 
    {
        $this->_templateFileName = $tmpFN;
    }
    
    public function assign($name, $mValue)
    {
        $this->_data[$name] = $mValue;
    }
    
    public function __set($name, $value) 
    {
        $this->assign($name, $value);
    }
    
    public static function addTitleChunk($text)
    {
        self::$_title[] = $text;
    }

    public static function getTitle()
    {
        return implode(controller::$config->info->delimeter, self::$_title);
    }
    
}

?>
