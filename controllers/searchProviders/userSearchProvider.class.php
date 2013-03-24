<?php
/**
 * Created by JetBrains PhpStorm.
 *
 * @author Kadet <kadet1090@gmail.com>
 * @package
 * @license WTFPL
 */

class userSearchProvider extends searchProvider {
    public $name = 'user';
    public $resultsTemplate = 'user/list.tpl';

    public function getResults($query, $page = 1, $config = array()) {
        $query = '%'.str_replace(array('_', '%', '.', '*'), array('\_', '\%', '_', '%'), $query).'%';
        $model = new userModel();

        $view = new HTMLview($this->resultsTemplate);
        $view->title = language::get('users');
        $view->users = $model->searchLimited($query, ($page - 1) * controller::$config->newsPerPage, (int)controller::$config->newsPerPage);
        $count = $model->getFoundCount();

        $view->count = $count;
        $view->page = $page;

        if($count > 0)
            return $view;
        else return null;
    }
}