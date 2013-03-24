<?php
/**
 * Created by JetBrains PhpStorm.
 *
 * @author Kadet <kadet1090@gmail.com>
 * @package 
 * @license WTFPL
 */

class newsSearchProvider extends searchProvider {
    public $name = 'news';
    public $resultsTemplate = 'news/search.tpl';

    public function getResults($query, $page = 1, $config = array()) {
        $query = '%'.str_replace(array('_', '%', '.', '*'), array('\_', '\%', '_', '%'), $query).'%';
        $model = new newsModel();

        $view = new HTMLview($this->resultsTemplate);
        $view->data = $model->searchLimited($query, language::getLang(), ($page - 1) * controller::$config->newsPerPage, (int)controller::$config->newsPerPage);
        $count = $model->getFoundCount();
        $view->count = $count;
        $view->page = $page;

        if($count > 0)
            return $view;
        else return null;
    }
}