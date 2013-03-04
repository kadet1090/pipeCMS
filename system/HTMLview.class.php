<?php
class HTMLview extends view
{
    /**
     * name => [src => string, body => string]
     * @var array
     */
    public static $additionalJS = array();
    
    /**
     * name => [src => string, body => string]
     * @var array
     */
    public static $additionalCSS = array();
    
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
        
        try {
            ob_start();
                include (self::$templateDir.DIRECTORY_SEPARATOR.$this->_templateFileName);
            return ob_get_clean();
        } catch (messageException $msg) {
            return controller::message($msg->getTitle(), $msg->getContent());
        } catch (Exception $e) { 
            return (string)$e;
        }
    }
    
    public function __toString()
    {
        return (string)$this->render();
    }
}
?>
