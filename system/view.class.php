<?php

abstract class view
{
    protected $_data = array();
    
    static public $templateDir = "templates";
    protected $_templateFileName;
    protected $_fallBackDir;
    public $parent;
    
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
        if($mValue instanceof view)
            $mValue->parent = $this;

        $this->_data[$name] = $mValue;
        return $this;
    }
    
    public function __set($name, $value) 
    {
        $this->assign($name, $value);
    }

    public function __get($name)
    {
        return $this->_data[$name];
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
