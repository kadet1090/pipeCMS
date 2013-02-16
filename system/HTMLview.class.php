<?php
class HTMLview extends view
{
    public function render() 
    {
        if(!isset($this->_templateFileName))
            throw new Exception('template isn\'t set', 1500);
        
        if(file_exists(self::$templateDir.DIRECTORY_SEPARATOR.'functions.php'))
            include_once self::$templateDir.DIRECTORY_SEPARATOR.'functions.php';
        
        extract($this->_data);
        
        $language = controller::$language;
        $config = controller::$config;
        $router = controller::$router;
        $currentUser = controller::$user;
        
        ob_start();
            include (self::$templateDir.DIRECTORY_SEPARATOR.$this->_templateFileName);
        return ob_get_clean();
    }
    
    public function __toString()
    {
        return $this->render();
    }
}
?>
