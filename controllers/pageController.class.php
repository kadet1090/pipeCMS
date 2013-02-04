<?php
class pageController extends controller
{
    public function __call($pageName, $arguments)
    {
	view::$robots = "all";
	if(!self::$router->match('pageShow'))  
	{
	    $pageModel = new pageModel();
	    $page = $pageModel->getByPublicID($pageName);

	    if($page)
	    {
		view::addTitleChunk($page->title);
		$view = new HTMLview('page/show.tpl');
		$page->content = BBcode::parse($page->content);

		$view->page = $page;
		return $view; // zwracanie bufora treści do strony
	    }
	    else
		throw new messageException(language::get('error'), language::get('errPageNotExist'));
	}
	else
	    throw new messageException(language::get('error'), language::get('errWrongURL'));
    }
}
?>
