<?php
class newsController extends controller
{
    public function page($params = array(), $data = array())
    {
        view::addTitleChunk(__('news'));
        view::$robots = "all";
        
        $newsModel = new newsModel(); // tworzenie modelu danych
        
        if(!isset($params['page'])) throw new messageException(__('error'), __('errWrongURL'));

        if($params['page'] > 1)
        {
            view::addTitleChunk(__('page'));
            view::addTitleChunk($params['page']);
        }

        $view = new HTMLview('news/all.tpl'); //tworzenie bufora treści
        $news = $newsModel->getLimited(($params['page'] - 1)  * (int)self::$config->newsPerPage, (int)self::$config->newsPerPage, language::getLang());
        if(empty($news))        throw new messageException(__('info'), __('noNews'), array('text' => ''));

        $view->news = (is_array($news) ? $news : array($news)); //...wartości
        $view->count = $newsModel->getNewsCount();
        $view->page = $params['page'];

        return $view; // zwracanie bufora treści do strony
    }
    
    public function index($params = array(), $data = array())
    {
        return $this->page(array('page' => 1));
    }
    
    public function show($params = array(), $data = array())
    {        
        view::addTitleChunk(__('news'));
        view::$robots = "all";
        
        if(!self::$router->match('news'))  throw new messageException(__('error'), __('errWrongURL'));
        
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
            throw new messageException(__('error'), __('errNewsNotExist'));
    }
    
    public function add($params = array(), $data = array())
    {
        view::addTitleChunk(__('news'));
        view::addTitleChunk(__('add'));
        view::$robots = "none";

        if(!self::$user->hasPermission('news/add')) // User hasn't permission? KILL HIM, em... throw exception.
            throw new messageException(__('error'), __('errNoPermission'), array('url' => array('news', 'add')));

        $newsModel = new newsModel();
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

            $view->categories = $categories;
            unset($_SESSION['backup']); // backup? Now? Who needs it?
            return $view;
        }
        else
        {
            backup::save('news_new', $data);
            $_SESSION['backup'] = serialize($data);

            $newsModel->add(htmlspecialchars($data['title']), (isset($data['html']) ? $data['content'] : htmlspecialchars($data['content'])), language::getLang(), self::$user->id, time(), $data['category']);
            return self::message(__('success'), __('addNewsSuccess'), array('url' => array('index', 'index')));
        }
    }
    
    public function edit($params = array(), $data = array())
    {
        view::addTitleChunk(__('news'));
        view::addTitleChunk(__('edit'));
        view::$robots = "none";

        if(!self::$router->match('news'))
            throw new messageException(__('error'), __('errWrongURL'), array('url' => array('index', 'index')));

        if(!self::$user->hasPermission('news/edit'))
            throw new messageException(__('error'), __('errNoPermission'));

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
                throw new messageException(__('error'), __('errNewsNotExist'));

            backup::load('news_'.$params['id']);

            $view->news = $news;
            $view->categories = $categories;
            return $view;
        }
        else
        {
            backup::save('news_'.$params['id'], $data);

            $newsModel->edit($params['id'], $data['title'], (isset($data['html']) ? $data['content'] : htmlspecialchars($data['content'])), $data['category']);
            return self::message(__('success'), __('editNewsSuccess'), array('url' => array('news', 'show', $params['name'], $params['id'])));
        }
    }
    
    public function delete($params = array(), $data = array())
    {
        view::addTitleChunk(__('news'));
        view::addTitleChunk(__('delete'));
        view::$robots = "none";
        
        if(!self::$router->match('news')) throw new messageException(__('error'), __('errWrongURL'), array('url' => array('index', 'index')));
        if(!self::$user->isLogged) throw new messageException(__('error'), __('errNotLoggedIn'), array('url' => array('index', 'index')));
        if(!self::$user->hasPermission('news/delete')) throw new messageException(__('error'), __('errNoPermission'), array('url' => array('index', 'index')));

        $newsModel = new newsModel();
        $newsModel->delete($params['id']);
        return self::message(__('success'), __('deleteNewsSuccess'), array('url' => array('index', 'index')));
    }
    
    public function category($params = array(), $data = array())
    {
        view::addTitleChunk(__('news'));
        view::addTitleChunk(__('category'));
        view::$robots = "all";
        
        if(self::$router->match('categoryPage') || self::$router->match('category'))
        {
            $newsModel = new newsModel();
            
            if(isset($params['page'])) $page = $params['page'];
            else $page = 1;
            
            $view = new HTMLview( 'news/all.tpl'); //tworzenie bufora treści
            $news = $newsModel->getLimitedFromCategory(($page - 1)  * (int)self::$config->newsPerPage,(int)self::$config->newsPerPage, language::getLang(), $params['id']);
            //if(empty($news))throw new messageException(__('info'), __('noNews'), array('text' => ''));
            
            $category = $newsModel->getCategory($params['id']);
            if(!$category)
                throw new messageException(__('error'), __('errCategoryNotExist'));
            
            view::addTitleChunk($category->name);
            $view->category = $category;
            $view->title = $category->name;
            $view->news = (is_array($news) ? $news : array($news)); //...wartości
            $view->count = $newsModel->getNewsCountFromCategory($params['id']);
            $view->page = $page;
            
            if($page > 1)
            {
                view::addTitleChunk(__('page'));
                view::addTitleChunk($page);
            }
            
            return $view;
        }
        else
            throw new messageException(__('error'), __('errWrongURL'), array('url' => array('index', 'index')));
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
        $news->author = new stdClass();
        $news->author->id = self::$user->id;
        $news->author->login = self::$user->login;
        $news->category = new stdClass();
        $news->category->id = $data['category'];
        $news->category->name = $category->name;

        $view = new HTMLview( 'news/preview.tpl');
        $view->news = $news;
        return $view; // zwracanie bufora treści do strony
    }
}
?>