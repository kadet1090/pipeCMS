<?php
class indexController extends controller
{
    public function index($params = array(), $data = array())
    {
        $view = new HTMLview('index.tpl');
        $newsController = new newsController();
        $view->news = $newsController->page(array('page' => 1));
        return $view;
    }
}
?>  