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
    
    static protected $_instance;
    static $language;

    protected static $_pattern = 'pattern.tpl';

    public function getErrorPage($errorCode)
    {
        $view = new HTMLview('error'.$errorCode.'.tpl');
        $view->info = self::$config->info;
        return $view;
    }
    
    public static function message($messageTitle, $messageContent, $nextPage = array())
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
