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

    private $_pluginManager;
    
    public function __construct() {
        $this->_loadConfig();
        $this->_initLanguage();
        $this->_prepareRouter();
        $this->_prepareDBConnection();
        $this->_getUser();

        BBcode::loadBBcode(new xml(self::$configDir.'BBcode.xml'));

        $this->_pluginManager = new pluginManager();
        $this->_pluginManager->loadPlugins();

        if(file_exists(view::$templateDir.'/functions.php'))
            include view::$templateDir.'/functions.php';

        hookManager::add('pipe_footer', 'cookies');

        searchController::$providers['news'] = new newsSearchProvider();
        searchController::$providers['user'] = new userSearchProvider();
    }
    
    public function work($startTime)
    {
        # load menu
        menu::$lang = language::getLang();

        # get required controller and action
        $controllerName = self::$router->controller.'Controller';
        $action = self::$router->action;

        # check if controller is exists and do properly action
        if(class_exists($controllerName) && $controllerName != __CLASS__)
        {
            try {
                $controller = new $controllerName();
                $page       = $controller->$action(self::$router->get(), self::$router->post());
            } catch(messageException $e) {
                $page           = new HTMLview('message.tpl');
                $page->message  = $e;
            }
        }
        else
        {
            $page = $this->getErrorPage(404);
        }

        $pattern = new HTMLview(self::$_pattern);
        $pattern->page = $page;

        # some statistics
        $pattern->gt    = round(microtime(true) - $startTime, 6);
        $pattern->sql   = dataBaseConnection::$ns;

        # set panel for user
        $pattern->userInfo = self::$user->isLogged ?
            new HTMLview('user/panel-info.tpl') :
            new HTMLview('user/panel-login-form.tpl');

        if(self::$user->isLogged) {
            $notifications = new notificationsModel();
            $pattern->notifications         = $notifications->getImportantNotifications(self::$user->id);
            $pattern->notificationCount     = $notifications->getNotificationsCount(self::$user->id);
        }

        # print our page
        echo $pattern->render();
    }
    
    protected function _initLanguage()
    {
        language::$langName = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : self::$config->defaultLanguage;
        
        if(isset($_GET['lang']))
        {
            setcookie('lang', $_GET['lang']);
            language::$langName = $_GET['lang'];
        }
        
        language::$langsDir = 'languages';
        language::load();
    }
    
    protected function _loadConfig()
    {
        self::$config = new mainConfig(new xml(self::$configDir.'config.xml'));
        view::$templateDir = view::$templateDir = 'templates/'.self::$config->defaultTemplate;
        view::addTitleChunk(self::$config->info->title);
    }
    
    protected function _prepareRouter()
    {
        # create and configure router instance
        self::$router = router::getInstance();
        self::$router->loadFromConfig(new config(new xml(self::$configDir.'router.config.xml')));
        
        # get url parameter from GET
        $URI = self::$router->get('q') != null ? 
               self::$router->get('q') : 
               array('index', 'index');
        
        # set URI and decode it
        self::$router->setURI($URI);
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
        
        if(isset($_SESSION['userid'])) {
            $id = $_SESSION['userid'];
        } elseif(autologin::get()) {
            $id = autologin::get();
        } else {
            return;
        }
        
        $userModel = new userModel;
        self::$user = $userModel->getUserData($id);

        if(self::$user->banned) // User banned?
        {
            session_destroy(); // Logout him...
            throw new messageException(language::get("error"), language::get("youHaveBeenBanned"), array("url" => array("index"))); // and pring massage
        }

        $userModel->updateLastActivity(time(), $id);
        self::$user->isLogged = true;
    }
}

?>
