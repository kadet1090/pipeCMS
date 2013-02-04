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
	    if(empty($page)) $page = self::$router->page;	else $page = 1;
	    if($page > 1)
	    {
		view::addTitleChunk(language::get('page'));
		view::addTitleChunk($page);
	    }
	    
	    $model = new userModel(); // tworzenie modelu danych
	    $usersCount = $model->getUsersCount();
	    $users = $model->getLimited(($page - 1)  * (int)self::$config->usersPerPage, (int)self::$config->usersPerPage);
	    if(empty($users))	throw new messageException(language::get('info'), language::get('noUsers'));
	    
	    $view = new HTMLview('user/list.tpl'); //tworzenie bufora treści
	    $view->title = language::get('users');
	    $view->users = (is_array($users) ? $users : array($users)); //...wartości
	    $view->pages = helperController::pageList($usersCount, (int)self::$config->usersPerPage, $page, array("users", "page"));
	    
	    return $view; // zwracanie bufora treści do strony
	}
	else
	    throw new messageException(language::get('error'), language::get('errWrongURL'));
    }
    
    public function inGroup($d = null)
    {
	view::$robots = "all";
	view::addTitleChunk(language::get('users'));
	$model = new userModel(); // tworzenie modelu danych
	if(self::$router->match('category') || self::$router->match('categoryPage') || !empty($d))
	{
	    if(empty($d)) $ID = self::$router->id;	else $ID = $d;
	    
	    $page = self::$router->page;
	    if(empty($page)) $page = 1;
	    if($page > 1)
	    {
		view::addTitleChunk(language::get('page'));
		view::addTitleChunk($page);
	    }
	    
	    $userCount = $model->getUsersCountInGroup($ID);
	    
	    $view = new HTMLview('user/group/inGroup.tpl'); //tworzenie bufora treści
	    $view->usersCount = $userCount;
	    if(empty($d)) $view->group = $model->getGroup($ID);
	    $users = $model->getLimitedFromGroup(($page - 1)  * (int)self::$config->usersPerPage, (int)self::$config->usersPerPage, (int)$ID);
	    if(empty($users))	throw new messageException(language::get('info'), language::get('noUsers'));
	    
	    $view->users = (is_array($users) ? $users : array($users)); //...wartości
	    $view->pages = helperController::pageList($userCount, (int)self::$config->usersPerPage, $page, array("users", "page"));
	    
	    return $view; // zwracanie bufora treści do strony
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

	    $group = $model->getGroup(self::$router->id);
	    $group->admin = $model->getUser($group->admin);
	    $view->group = $group;
	    $view->users = $this->inGroup(self::$router->id);
	    return $view;
	}
	else
	    throw new messageException(language::get('error'), language::get('errWrongURL'));
    }
}
?>