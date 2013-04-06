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
        
        extract($this->_data);
        
        $language = controller::$language;
        $config = controller::$config;
        $router = controller::$router;
        $currentUser = controller::$user;

        $path = file_exists(self::$templateDir.'/'.$this->_templateFileName) ?
            self::$templateDir.'/'.$this->_templateFileName :
            $this->_fallBackDir.'/'.$this->_templateFileName;

        if(!file_exists($path))
            return 'Error while loading '.$this->_templateFileName.' template.';

        try {
            ob_start();
                include ($path);
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

    public static function addJs($body = null, $src = null)
    {
        self::$additionalJS[] = (object)array('body' => $body, 'src' => $src);
    }

    public static function addCss($body = null, $src = null)
    {
        self::$additionalCSS[] = (object)array('body' => $body, 'src' => $src);
    }
}
?>
