<?php
class controller
{    
    /**
     * 
     * @var view 
     */
    public static $view;
    
    /**
     *
     * @var config 
     */
    public static $config;
    
    /**
     *
     * @var user 
     */
    public static $user;
    
    /**
     *
     * @var router 
     */
    public static $router;
    
    public static $plugins = array(
        "syntax"    => "plugins/syntax/"
    );
    
    static protected $_instance;
    static $language;
    
    public function getErrorPage($errorCode)
    {
        $view = new HTMLview('error'.$errorCode.'.tpl');
        $view->info = self::$config->info;
        return $view;
    }
    
    public function message($messageTitle, $messageContent, $nextPage = array())
    {
        $view = new HTMLview('message.tpl');
        $view->message = new message($messageTitle, $messageContent, $nextPage);
        return $view;
    }
    
    public function __call($name, $arguments)
    {
        return $this->getErrorPage(404);
    }
}
?>
