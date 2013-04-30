<?php
class pageController extends controller
{
    public function __call($pageName, $arguments)
    {
        view::$robots = "all";

        if(self::$router->match('pageShow')) throw new messageException(language::get('error'), language::get('errWrongURL'));

        $pageModel = new pageModel();
        $page = $pageModel->getByPublicID($pageName);

        if(!$page) throw new messageException(language::get('error'), language::get('errPageNotExist'));
        view::addTitleChunk($page->title);
        $view = new HTMLview('page/show.tpl');
        $page->content = BBcode::parse($page->content);

        $view->page = $page;

        return $view;
    }
}
?>
