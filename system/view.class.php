<?php

abstract class view
{
    protected $_data = array();
    
    static public $templateDir;
    protected $_templateFileName;
    protected $_fallBackDir;
    
    static $robots;
    static protected $_title;
    
    abstract public function render();
    
    public function setTemplateFileName($tmpFN)
    {
        $this->_templateFileName = $tmpFN;
        return $this;
    }
    
    public function __construct($tmpFN = '', $fallBackDir = null)
    {
        $this->_templateFileName = $tmpFN;
        $this->_fallBackDir = $fallBackDir;
    }
    
    public function assign($name, $mValue)
    {
        $this->_data[$name] = $mValue;
        return $this;
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
