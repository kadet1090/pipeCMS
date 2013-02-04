<?php
class newsController extends controller
{
    public function page($page = null)
    {
	view::addTitleChunk(language::get('news'));
	view::$robots = "all";
	
	$newsModel = new newsModel(); // tworzenie modelu danych
	
	if(self::$router->match('page') || $page != null)
	{
	    if(empty($page)) $page = self::$router->page;
	    
	    if($page > 1)
	    {
		view::addTitleChunk(language::get('page'));
		view::addTitleChunk($page);
	    }
	    
	    $view = new HTMLview('news/all.tpl'); //tworzenie bufora treści
	    $news = $newsModel->getLimited(($page - 1)  * (int)self::$config->newsPerPage, (int)self::$config->newsPerPage, language::getLang());
	    if(empty($news))	throw new messageException(language::get('info'), language::get('noNews'), array('text' => ''));

	    $newsCount = $newsModel->getNewsCount();
	    $view->news = (is_array($news) ? $news : array($news)); //...wartości
	    $view->newsCount = $newsCount;
	    
	    $pageCount = ceil($newsCount / self::$config->newsPerPage);
	    $sp = ($page - 3 > 0 ? $page - 3 : 1);
	    $ep = ceil($sp + ($pageCount < 7 ? $pageCount : 7));
	    
	    
	    $view->startPage = ($sp > 0 ? $sp : 1);
	    $view->currentPage = $page;
	    $view->endPage = $ep;
	    
	    return $view; // zwracanie bufora treści do strony
	}
	else throw new messageException(language::get('error'), language::get('errWrongURL'));
    }
    
    public function index()
    {
    	return $this->page(1);
    }
    
    public function show()
    {	
	view::addTitleChunk(language::get('news'));
	view::$robots = "all";
	
	if(!self::$router->match('news'))  throw new messageException(language::get('error'), language::get('errWrongURL'));
	
	$newsModel = new newsModel();
	$news = $newsModel->get(self::$router->id);
	$newsModel->increaseViews(self::$router->id);
	
	if($news)
	{
	    view::addTitleChunk($news->title);
	    $view = new HTMLview( 'news/news.tpl');
	    $news->content = BBcode::parse($news->content);
	    
	    $view->news = $news;
	    return $view; // zwracanie bufora treści do strony
	}
	else
	    throw new messageException(language::get('error'), language::get('errNewsNotExist'));
    }
    
    public function add()
    {
	view::addTitleChunk(language::get('news'));
	view::addTitleChunk(language::get('add'));
	view::$robots = "none";
	
	$newsModel = new newsModel();
	if(self::$user->isLogged)
	{
	    if(self::$user->hasPermission('news/add'))
	    {
		if(self::$router->post('preview') != null)
		{
		    return $this->_preview();
		    
		}
		elseif(self::$router->post('submit') == null)
		{
		    $categories = $newsModel->getCategories();
		    $view = new HTMLview( 'news/add-form.tpl');
		    if(!empty($_SESSION['backup'])) $view->backup = unserialize($_SESSION['backup']);
		    else $view->backup = array();
		    $view->categories = (is_array($categories) ? $categories : array($categories));
		    unset($_SESSION['backup']);
		    return $view;
		}
		else
		{
		    $_SESSION['backup'] = serialize(self::$router->post());
		    
		    if(self::$router->post('title') == null)	    throw new messageException(language::get('error'), language::get('errTitleNotSet'));
		    if(self::$router->post('content') == null)	    throw new messageException(language::get('error'), language::get('errContentNotSet'));
		    if(self::$router->post('category') == null)    throw new messageException(language::get('error'), language::get('errCategoryNotSet'));
		    
		    $newsModel->add(htmlspecialchars(self::$router->post('title')), (self::$router->post('html') != NULL ? self::$router->post('content') : htmlspecialchars(self::$router->post('content'))), language::getLang(), self::$user->id, time(), self::$router->post('category'));
		    throw new messageException(language::get('success'), language::get('addNewsSuccess'), array('url' => array('index', 'index')));
		}
	    }
	    else
		throw new messageException(language::get('error'), language::get('errNoPermission'), array('url' => array('news', 'add')));
	}
	else
	    throw new messageException(language::get('error'), language::get('errNotLoggedIn'), array('url' => array('index', 'index')));
    }
    
    public function edit()
    {
	view::addTitleChunk(language::get('news'));
	view::addTitleChunk(language::get('edit'));
	view::$robots = "none";
	
	if(self::$router->match('news'))
	{
	    if(self::$user->isLogged)
	    {
		if(self::$user->hasPermission('news/edit'))
		{
		    $newsModel = new newsModel();
		    
		    if(self::$router->post('submit') == null)
		    {
			$categories = $newsModel->getCategories();
			$view = new HTMLview( 'news/edit-form.tpl');
			$view->news = $newsModel->get(self::$router->id);
			$view->categories = (is_array($categories) ? $categories : array($categories));
			return $view;
		    }
		    else
		    {
			if(self::$router->post('title') == null)throw new messageException(language::get('error'), language::get('errTitleNotSet'), array('url' => array('news', 'edit', self::$router->name, self::$router->id)));
			if(self::$router->post('content') == null)throw new messageException(language::get('error'), language::get('errContentNotSet'), array('url' => array('news', 'edit', self::$router->name, self::$router->id)));
			if(self::$router->post('category') == null)throw new messageException(language::get('error'), language::get('errCategoryNotSet'), array('url' => array('news', 'edit', self::$router->name, self::$router->id)));
			
			$newsModel->edit(self::$router->id, self::$router->post('title'), (self::$router->post('html') != NULL ? self::$router->post('content') : htmlspecialchars(self::$router->post('content'))), self::$router->post('category'));
			throw new messageException(language::get('success'), language::get('editNewsSuccess'), array('url' => array('index', 'index')));
		    }
		}
		else
		    throw new messageException(language::get('error'), language::get('errNoPermission'), array('url' => array('index', 'index')));
	    }
	    else
		throw new messageException(language::get('error'), language::get('errNotLoggedIn'), array('url' => array('index', 'index')));
	}
	else
	    throw new messageException(language::get('error'), language::get('errWrongURL'), array('url' => array('index', 'index')));
    }
    
    public function delete()
    {
	view::addTitleChunk(language::get('news'));
	view::addTitleChunk(language::get('delete'));
	view::$robots = "none";
	
	if(self::$router->match('news'))
	{
	    if(self::$user->isLogged)
	    {
		if(self::$user->hasPermission('news/delete'))
		{
		    $newsModel = new newsModel();
		    $newsModel->delete(self::$router->id);
		    throw new messageException(language::get('success'), language::get('deleteNewsSuccess'), array('url' => array('index', 'index')));
		}
		else
		    throw new messageException(language::get('error'), language::get('errNoPermission'), array('url' => array('index', 'index')));
	    }
	    else
		throw new messageException(language::get('error'), language::get('errNotLoggedIn'), array('url' => array('index', 'index')));
	}
	else
	    throw new messageException(language::get('error'), language::get('errWrongURL'), array('url' => array('index', 'index')));
    }
    
    public function category()
    {
	view::addTitleChunk(language::get('news'));
	view::addTitleChunk(language::get('category'));
	view::$robots = "all";
	
	if(self::$router->match('categoryPage') || self::$router->match('category'))
	{
	    $newsModel = new newsModel();
	    
	    if(self::$router->page != null) $page = self::$router->page;
	    else $page = 1;
	    
	    $view = new HTMLview( 'news/all.tpl'); //tworzenie bufora treści
	    $news = $newsModel->getLimitedFromCategory(($page - 1)  * (int)self::$config->newsPerPage,(int)self::$config->newsPerPage, language::getLang(), self::$router->id);
	    if(empty($news))throw new messageException(language::get('info'), language::get('noNews'), array('text' => ''));
	    
	    $category = $newsModel->getCategory(self::$router->id);
	    view::addTitleChunk($category->name);
	    $view->category = $category;
	    $view->title = $category->name;
	    $view->news = (is_array($news) ? $news : array($news)); //...wartości
	    $view->newsCount = $newsModel->getNewsCountFromCategory(self::$router->id);
	    
	    $pageCount = ceil($newsModel->getNewsCountFromCategory(self::$router->id) / self::$config->newsPerPage);
	    $sp = ($page - 3 > 0 ? $page - 3 : 1);
	    $ep = ceil($sp + ($pageCount < 7 ? $pageCount : 7));
	    
	    $view->startPage = $sp;
	    $view->currentPage = $page;
	    $view->endPage = $ep;
	    
	    if($page > 1)
	    {
		view::addTitleChunk(language::get('page'));
		view::addTitleChunk($page);
	    }
	    
	    return $view;
	}
	else
	    throw new messageException(language::get('error'), language::get('errWrongURL'), array('url' => array('index', 'index')));
    }
    
    protected function _preview()
    {
	$newsModel = new newsModel();
	$category = $newsModel->getCategory(self::$router->post('category'));
	$news = new stdClass();

	$_SESSION['backup'] = serialize(self::$router->post());

	$news->added = time();
	$news->views = 0;
	$news->content = BBcode::parse((self::$router->post('html') == NULL ? stripslashes(self::$router->post('content')) : htmlspecialchars(self::$router->post('content'))));
	$news->title = self::$router->post('title');
	$news->author = self::$user->id;
	$news->authorName = self::$user->login;
	$news->category = self::$router->post('category');
	$news->categoryName = $category->name;

	$view = new HTMLview( 'news/preview.tpl');
	$view->news = $news;
	return $view; // zwracanie bufora treści do strony
    }
}
?>