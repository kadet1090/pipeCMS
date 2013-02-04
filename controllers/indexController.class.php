<?php
class indexController extends controller
{
    public function index()
    {
	$view = new HTMLview('index.tpl');
	$newsController = new newsController();
	$view->news = $newsController->page(1);
	return $view;
    }
}
?>  