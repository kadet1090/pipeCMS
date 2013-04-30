<?php
class language
{
    static protected $_phrases;
    
    static public $langsDir;
    static public $langName;
    
    static public function load()
    {
        if(empty(self::$langsDir))
            throw new Exception ('You didn\'t set languages directory!', 1600);
        if(empty(self::$langName))    
            throw new Exception ('You didn\'t set language name!', 1601);
        
        if(!file_exists(self::$langsDir.DIRECTORY_SEPARATOR.self::$langName.'.ini'))
            throw new Exception ('Language file not exist!', 1601);
        
        self::$_phrases = parse_ini_file(self::$langsDir.DIRECTORY_SEPARATOR.self::$langName.'.ini');
    }
    
    static public function get($phraseName, $args = array())
    {
        return vsprintf(isset(self::$_phrases[$phraseName]) ? self::$_phrases[$phraseName] : '#'.$phraseName, $args);
    }
    
    static public function getLang() 
    {
        return self::$langName;
    }
    
    static public function available() 
    {
        return array_values(str_replace('.ini', '', array_diff(scandir(self::$langsDir), array('.', '..'))));
    }
}
?>
