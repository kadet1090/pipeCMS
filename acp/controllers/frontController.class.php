<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of frontController
 *
 * @author Kadet
 */
class frontController extends controller {
    /**
     * Directory with config files
     * @var string
     */
    public static $configDir = 'cfg/';
    
    public function __construct() {
        $this->_loadConfig();
        $this->_initLanguage();
        $this->_prepareRouter();
        $this->_prepareDBConnection();
        $this->_getUser();
        
        BBcode::loadBBcode(new xml(self::$configDir.'BBcode.xml'));
    }
    
    public function work($startTime)
    {
        # get required views
        $pattern    = new HTMLview('pattern.tpl');
        
        # add "ACP" chunk to title
        view::addTitleChunk("ACP");
        
        # get required controller and action
        $controllerName = self::$router->controller.'Controller';
        $action = self::$router->action;
        
        # and display login panel if user is not logged in.
        if(!self::$user->isLogged)
        {
            $controllerName = 'adminController';
            $action = 'login';
        }
        
        # check if controller is exists and do properly action
        if(class_exists($controllerName) && $controllerName != __CLASS__)
        {
            try {
                $controller         = new $controllerName();
                $pattern->page      = $controller->$action();
            } catch(messageException $e) {
                $message            = new HTMLview('message.tpl');
                $message->message   = $e;
                $pattern->page      = $message;
            }
        }
        else
        {
            $controller = new controller();
            $pattern->page = $controller->getErrorPage(404);
        }
        
        # some statistics
        $pattern->gt    = round(microtime(true) - $startTime, 6);
        $pattern->sql   = dataBaseConnection::$ns;
        
        # print our page
        echo $pattern;
    }
    
    protected function _initLanguage()
    {
        language::$langName = 'pl';
        language::$langsDir = '../languages';
        language::load();
    }
    
    protected function _loadConfig()
    {
        self::$config = new mainConfig(new xml(self::$configDir.'config.xml'));
        view::$templateDir = 'templates/'.self::$config->defaultACPTemplate;
        view::addTitleChunk(self::$config->info->title);
    }
    
    protected function _prepareRouter()
    {
        # create and configure router instance
        self::$router = router::getInstance();
        self::$router->loadFromConfig(new config(new xml(self::$configDir.'router.config.xml')));
        
        # get url parameter from GET
        $uRI = self::$router->get('q') != null ? 
                self::$router->get('q') : 
                array('index', 'index');
        
        # set URI and decode it
        self::$router->setURI($uRI);
        self::$router->decodeURI();
    }
    
    protected function _prepareDBConnection()
    {
        dataBaseConnection::$log = new log("dblog.log");
        $bconnection = new dataBaseConnection();
        $bconnection->loadConfig(new config(new xml(self::$configDir.'dataBase.config.xml')));
        $bconnection->connect();
        dataBaseModel::$connection = $bconnection;
    }
    
    protected function _getUser()
    {
        self::$user = new user();
        
        if(isset($_SESSION['userid'])) // Is user logged in?
        {
            $userModel = new userModel;
            self::$user = $userModel->getUserData($_SESSION['userid']);

            if(self::$user->banned) // User banned?
            {
                session_destroy(); // Logout him...
                throw new messageException(language::get("error"), language::get("youHaveBeenBanned"), array("url" => array("index"))); // and pring massage
            }

            $userModel->updateLastActivity(time(), $_SESSION['userid']);
            self::$user->isLogged = true;
        }
    }
}

?>
