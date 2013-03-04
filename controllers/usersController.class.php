<?php
class usersController extends controller
{   
    
    public function index($params = array(), $data = array()) { return $this->page(array('page' => 1)); }
    
    public function page($params = array(), $data = array())
    {
        view::$robots = "all";
        view::addTitleChunk(language::get('users'));
        
        if($params['page'] != null || self::$router->match('page'))
        {
            if($params['page'] > 1)
            {
                view::addTitleChunk(language::get('page'));
                view::addTitleChunk($params['page']);
            }
            
            $model = new userModel(); // tworzenie modelu danych
            $usersCount = $model->getUsersCount();
            $users = $model->getLimited(($params['page'] - 1)  * (int)self::$config->usersPerPage, (int)self::$config->usersPerPage);
            if(empty($users))        throw new messageException(language::get('info'), language::get('noUsers'));
            
            $view = new HTMLview('user/list.tpl'); //tworzenie bufora treści
            $view->title = language::get('users');
            $view->page = $params['page'];
            $view->count = $usersCount;
            $view->users = (is_array($users) ? $users : array($users)); //...wartości
            
            return $view; // zwracanie bufora treści do strony
        }
        else
            throw new messageException(language::get('error'), language::get('errWrongURL'));
    }
    
    public function inGroup($params = array(), $data = array())
    {
        view::$robots = "all";
        view::addTitleChunk(language::get('users'));
        $model = new userModel(); // tworzenie modelu danych
        if(self::$router->match('category') || self::$router->match('categoryPage') || !empty($params['id']))
        {
            $page = isset($params['page']) ? $params['page'] : 1;
            if($page > 1)
            {
                view::addTitleChunk(language::get('page'));
                view::addTitleChunk($page);
            }
            
            if(empty($params['group']))
                $group = $model->getGroup($params['id'], "%|{$params['id']}|%");
            else
                $group = $params['group'];
            
            $userCount = $group->count;
            
            $view = new HTMLview('user/group/inGroup.tpl');
            $view->usersCount = $userCount;
            $view->group = $group;
            
            $users = $model->getLimitedFromGroup(($page - 1)  * (int)self::$config->usersPerPage, (int)self::$config->usersPerPage, '%|'.$params['id'].'|%');
            
            $view->users = $users;
            $view->pages = helperController::pageList($userCount, (int)self::$config->usersPerPage, $page, array("users", "page"));
            
            return $view;
        }
        else
            throw new messageException(language::get('error'), language::get('errWrongURL'));
    }
    
    public function group($params = array(), $data = array())
    {
        view::$robots = "all";
        view::addTitleChunk(language::get('users'));
        view::addTitleChunk(language::get('group'));
        
        if(self::$router->match('category'))
        {
            $model = new userModel;
            $view = new HTMLview('user/group/overview.tpl'); //tworzenie bufora treśc

            $group = $model->getGroup($params['id'], '%|'.$params['id'].'|%');
            
            if(!empty($group->admin))
                $group->admin = $model->getUser($group->admin);
            
            $view->group = $group;
            $view->users = $this->inGroup(array('id' => $params['id'], 'group' => $group));
            return $view;
        }
        else
            throw new messageException(language::get('error'), language::get('errWrongURL'));
    }
}
?>
