<?php
class helperController extends controller
{
    public function __construct() {
        self::$_pattern = 'simple.tpl';
    }

    public function userSelect($params, $data)
    {
        if(isset($params['page'])) $page = $params['page'];        else $page = 1;
        if($page > 1)
        {
            view::addTitleChunk(language::get('page'));
            view::addTitleChunk($page);
        }

        $model = new userModel(); // tworzenie modelu danych

        if(isset($data['search']))
            $users = $model->searchLimited(str_replace('*', '%', $data['search']), ($page - 1)  * (int)self::$config->usersPerPage, (int)self::$config->usersPerPage);
        else
            $users = $model->getLimited(($page - 1)  * (int)self::$config->usersPerPage, (int)self::$config->usersPerPage);

        if(empty($users))        throw new messageException(language::get('info'), language::get('noUsers'));

        $view = new HTMLview('helpers/userSelector.tpl'); //tworzenie bufora treści
        $view->title = language::get('users');
        $view->users = (is_array($users) ? $users : array($users)); //...wartości
        $view->page = $page;
        $view->count = $model->getFoundCount();

        return $view; // zwracanie bufora treści do strony
    }
    
    public static function pageList($count, $perPage, $currentPage, array $url)
    {
        $view = new HTMLview("page-list.tpl");
        $pageCount = ceil($count / $perPage);
        $startPage = ($currentPage - 3 > 0 ? $currentPage - 3 : 1);
        $endPage = ($startPage + 6 < $pageCount ? $startPage + 6 : $pageCount);
        $startPage = $endPage - 6;
        
        $view->url = $url;
        $view->perPage = $perPage;
        $view->count = $count;
        $view->startPage = ($startPage > 0 ? $startPage : 1);
        $view->currentPage = $currentPage;
        $view->endPage = $endPage;
        
        return $view;
    }
}

?>
