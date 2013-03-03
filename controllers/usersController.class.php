<?php
class usersController extends controller
{   
    
    public function index() { return $this->page(1); }
    
    public function page($page = null)
    {
        view::$robots = "all";
        view::addTitleChunk(language::get('users'));
        
        if($page != null || self::$router->match('page'))
        {
            if(empty($page)) $page = self::$router->page;        else $page = 1;
            if($page > 1)
            {
                view::addTitleChunk(language::get('page'));
                view::addTitleChunk($page);
            }
            
            $model = new userModel(); // tworzenie modelu danych
            $usersCount = $model->getUsersCount();
            $users = $model->getLimited(($page - 1)  * (int)self::$config->usersPerPage, (int)self::$config->usersPerPage);
            if(empty($users))        throw new messageException(language::get('info'), language::get('noUsers'));
            
            $view = new HTMLview('user/list.tpl'); //tworzenie bufora treści
            $view->title = language::get('users');
            $view->page = $page;
            $view->count = $usersCount;
            $view->users = (is_array($users) ? $users : array($users)); //...wartości
            
            return $view; // zwracanie bufora treści do strony
        }
        else
            throw new messageException(language::get('error'), language::get('errWrongURL'));
    }
    
    public function inGroup($ID = null, $group = null)
    {
        view::$robots = "all";
        view::addTitleChunk(language::get('users'));
        $model = new userModel(); // tworzenie modelu danych
        if(self::$router->match('category') || self::$router->match('categoryPage') || !empty($d))
        {
            if(empty($ID)) $ID = self::$router->id;
            
            $page = self::$router->page;
            if(empty($page)) $page = 1;
            if($page > 1)
            {
                view::addTitleChunk(language::get('page'));
                view::addTitleChunk($page);
            }
            if(empty($group))
                $group = $model->getGroup($ID, "%|{$ID}|%");
            
            $userCount = $group->count;
            
            $view = new HTMLview('user/group/inGroup.tpl');
            $view->usersCount = $userCount;
            $view->group = $group;
            
            $users = $model->getLimitedFromGroup(($page - 1)  * (int)self::$config->usersPerPage, (int)self::$config->usersPerPage, '%|'.$ID.'|%');
            
            $view->users = $users;
            $view->pages = helperController::pageList($userCount, (int)self::$config->usersPerPage, $page, array("users", "page"));
            
            return $view;
        }
        else
            throw new messageException(language::get('error'), language::get('errWrongURL'));
    }
    
    public function group()
    {
        view::$robots = "all";
        view::addTitleChunk(language::get('users'));
        view::addTitleChunk(language::get('group'));
        
        if(self::$router->match('category'))
        {
            $model = new userModel;
            $view = new HTMLview('user/group/overview.tpl'); //tworzenie bufora treśc

            $group = $model->getGroup(self::$router->id, '%|'.self::$router->id.'|%');
            
            if(!empty($group->admin))
                $group->admin = $model->getUser($group->admin);
            
            $view->group = $group;
            $view->users = $this->inGroup(self::$router->id, $group);
            return $view;
        }
        else
            throw new messageException(language::get('error'), language::get('errWrongURL'));
    }
}
?>
