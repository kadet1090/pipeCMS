<?php
class helperController extends controller
{
    public function userSelect()
    {
        controller::$view = new HTMLview("helpers/pattern.tpl");
        if(self::$router->page != null) $page = self::$router->page;        else $page = 1;
        if($page > 1)
        {
            view::addTitleChunk(language::get('page'));
            view::addTitleChunk($page);
        }

        $model = new userModel(); // tworzenie modelu danych
        $usersCount = $model->getUsersCount();
        $users = $model->getLimited(($page - 1)  * (int)self::$config->usersPerPage, (int)self::$config->usersPerPage);
        if(empty($users))        throw new messageException(language::get('info'), language::get('noUsers'));

        $view = new HTMLview('helpers/userSelector.tpl'); //tworzenie bufora treści
        $view->title = language::get('users');
        $view->users = (is_array($users) ? $users : array($users)); //...wartości
        $view->pages = helperController::pageList($usersCount, (int)self::$config->usersPerPage, $page, array("helper", "userSelect", "page"));

        return $view; // zwracanie bufora treści do strony        
        $view = new HTMLview("helpers/userSelctor.tpl");
        return $view;
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
