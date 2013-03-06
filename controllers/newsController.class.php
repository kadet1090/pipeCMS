<?php
class newsController extends controller
{
    public function page($params = array(), $data = array())
    {
        view::addTitleChunk(language::get('news'));
        view::$robots = "all";
        
        $newsModel = new newsModel(); // tworzenie modelu danych
        
        if(isset($params['page']))
        {
            if($params['page'] > 1)
            {
                view::addTitleChunk(language::get('page'));
                view::addTitleChunk($params['page']);
            }
            
            $view = new HTMLview('news/all.tpl'); //tworzenie bufora treści
            $news = $newsModel->getLimited(($params['page'] - 1)  * (int)self::$config->newsPerPage, (int)self::$config->newsPerPage, language::getLang());
            if(empty($news))        throw new messageException(language::get('info'), language::get('noNews'), array('text' => ''));

            $newsCount = $newsModel->getNewsCount();
            $view->news = (is_array($news) ? $news : array($news)); //...wartości
            $view->newsCount = $newsCount;
            
            $pageCount = ceil($newsCount / self::$config->newsPerPage);
            $sp = ($params['page'] - 3 > 0 ? $params['page'] - 3 : 1);
            $ep = ceil($sp + ($pageCount < 7 ? $pageCount : 7));
            
            
            $view->startPage = ($sp > 0 ? $sp : 1);
            $view->currentPage = $params['page'];
            $view->endPage = $ep;
            
            return $view; // zwracanie bufora treści do strony
        }
        else throw new messageException(language::get('error'), language::get('errWrongURL'));
    }
    
    public function index($params = array(), $data = array())
    {
            return $this->page(array('page' => 1));
    }
    
    public function show($params = array(), $data = array())
    {        
        view::addTitleChunk(language::get('news'));
        view::$robots = "all";
        
        if(!self::$router->match('news'))  throw new messageException(language::get('error'), language::get('errWrongURL'));
        
        $newsModel = new newsModel();
        $news = $newsModel->get($params['id']);
        $newsModel->increaseViews($params['id']);
        
        if($news)
        {
            view::addTitleChunk($news->title);
            $view = new HTMLview('news/news.tpl');
            $news->content = BBcode::parse($news->content);
            
            $view->news = $news;
            return $view; // zwracanie bufora treści do strony
        }
        else
            throw new messageException(language::get('error'), language::get('errNewsNotExist'));
    }
    
    public function add($params = array(), $data = array())
    {
        view::addTitleChunk(language::get('news'));
        view::addTitleChunk(language::get('add'));
        view::$robots = "none";
        
        $newsModel = new newsModel();
        if(self::$user->isLogged)
        {
            if(self::$user->hasPermission('news/add'))
            {
                if(isset($data['preview']))
                {
                    backup::save('news_new', $data);
                    return $this->_preview($params, $data);
                }
                elseif(!isset($data['submit']))
                {
                    $categories = $newsModel->getCategories();
                    $view = new HTMLview('news/add-form.tpl');
                    
                    backup::load('news_new');
                    
                    $view->categories = (is_array($categories) ? $categories : array($categories));
                    unset($_SESSION['backup']);
                    return $view;
                }
                else
                {
                    backup::save('news_new', $data);
                    $_SESSION['backup'] = serialize($data);
                    
                    if(!isset($data['title']))       throw new messageException(language::get('error'), language::get('errTitleNotSet'));
                    if(!isset($data['content']))     throw new messageException(language::get('error'), language::get('errContentNotSet'));
                    if(!isset($data['category']))    throw new messageException(language::get('error'), language::get('errCategoryNotSet'));
                    
                    $newsModel->add(htmlspecialchars($data['title']), (isset($data['html']) ? $data['content'] : htmlspecialchars($data['content'])), language::getLang(), self::$user->id, time(), $data['category']);
                    throw new messageException(language::get('success'), language::get('addNewsSuccess'), array('url' => array('index', 'index')));
                }
            }
            else
                throw new messageException(language::get('error'), language::get('errNoPermission'), array('url' => array('news', 'add')));
        }
        else
            throw new messageException(language::get('error'), language::get('errNotLoggedIn'), array('url' => array('index', 'index')));
    }
    
    public function edit($params = array(), $data = array())
    {
        view::addTitleChunk(language::get('news'));
        view::addTitleChunk(language::get('edit'));
        view::$robots = "none";
        
        if(self::$router->match('news'))
        {
            if(self::$user->hasPermission('news/edit'))
            {
                $newsModel = new newsModel();

                if(isset($data['preview']))
                {
                    backup::save('news_'.$params['id'], $data);
                    return $this->_preview($params, $data);
                }
                elseif(!isset($data['submit']))
                {
                    $categories = $newsModel->getCategories();
                    $view = new HTMLview('news/edit-form.tpl');
                    $news = $newsModel->get($params['id']);
                    
                    if(!$news)
                        throw new messageException(language::get('error'), language::get('errNewsNotExist'));
                    
                    backup::load('news_'.$params['id']);
                    
                    $view->news = $news;
                    $view->categories = $categories;
                    return $view;
                }
                else
                {
                    backup::save('news_'.$params['id'], $data);
                    
                    if(!isset($data['title']))throw new messageException(language::get('error'), language::get('errTitleNotSet'), array('url' => array('news', 'edit', $params['name'], $params['id'])));
                    if(!isset($data['content']))throw new messageException(language::get('error'), language::get('errContentNotSet'), array('url' => array('news', 'edit', $params['name'], $params['id'])));
                    if(!isset($data['category']))throw new messageException(language::get('error'), language::get('errCategoryNotSet'), array('url' => array('news', 'edit', $params['name'], $params['id'])));

                    $newsModel->edit($params['id'], $data['title'], (isset($data['html']) ? $data['content'] : htmlspecialchars($data['content'])), $data['category']);
                    throw new messageException(language::get('success'), language::get('editNewsSuccess'), array('url' => array('index', 'index')));
                }
            }
            else
                throw new messageException(language::get('error'), language::get('errNoPermission'), array('url' => array('index', 'index')));
        }
        else
            throw new messageException(language::get('error'), language::get('errWrongURL'), array('url' => array('index', 'index')));
    }
    
    public function delete($params = array(), $data = array())
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
                    $newsModel->delete($params['id']);
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
    
    public function category($params = array(), $data = array())
    {
        view::addTitleChunk(language::get('news'));
        view::addTitleChunk(language::get('category'));
        view::$robots = "all";
        
        if(self::$router->match('categoryPage') || self::$router->match('category'))
        {
            $newsModel = new newsModel();
            
            if($params['page'] != null) $page = $params['page'];
            else $page = 1;
            
            $view = new HTMLview( 'news/all.tpl'); //tworzenie bufora treści
            $news = $newsModel->getLimitedFromCategory(($page - 1)  * (int)self::$config->newsPerPage,(int)self::$config->newsPerPage, language::getLang(), $params['id']);
            //if(empty($news))throw new messageException(language::get('info'), language::get('noNews'), array('text' => ''));
            
            $category = $newsModel->getCategory($params['id']);
            if(!$category)
                throw new messageException(language::get('error'), language::get('errCategoryNotExist'));
            
            view::addTitleChunk($category->name);
            $view->category = $category;
            $view->title = $category->name;
            $view->news = (is_array($news) ? $news : array($news)); //...wartości
            $view->newsCount = $newsModel->getNewsCountFromCategory($params['id']);
            
            $pageCount = ceil($newsModel->getNewsCountFromCategory($params['id']) / self::$config->newsPerPage);
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
    
    protected function _preview($params = array(), $data = array())
    {
        $newsModel = new newsModel();
        $category = $newsModel->getCategory($data['category']);
        $news = new stdClass();

        $_SESSION['backup'] = serialize($data);

        $news->added = time();
        $news->views = 0;
        $news->content = BBcode::parse((!isset($data['html']) ? stripslashes($data['content']) : htmlspecialchars($data['content'])));
        $news->title = $data['title'];
        $news->author = self::$user->id;
        $news->authorName = self::$user->login;
        $news->category = $data['category'];
        $news->categoryName = $category->name;

        $view = new HTMLview( 'news/preview.tpl');
        $view->news = $news;
        return $view; // zwracanie bufora treści do strony
    }
}
?>