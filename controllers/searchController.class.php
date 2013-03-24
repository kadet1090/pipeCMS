<?php
class searchController extends controller
{
    /**
     * @var array[searchProvider]
     */
    static $providers = array();

    public function index($params = array(), $data = array())
    {
        $_SESSION['search_data'] = null;
        return new HTMLview('search/index.tpl');
    }

    public function results($params = array(), $data = array()) {
        if(!isset($data['query'])) return false;

        $_SESSION['search_data'] = serialize($data);

        $results = array();
        foreach(self::$providers as $provider) {
            $result = $provider->getResults($data['query'], 1, array_grep($data, '/^'.$provider->name.'_(.*)$/si'));
            if(!empty($result)) $results[] = $result;
        }

        $view = new HTMLview('search/results.tpl');
        $view->results = $results;
        $view->query = $data['query'];
        return $view;
    }

    public function __call($name, $arguments) {
        if(!isset(self::$providers[$name]))
            throw new messageException(language::get('error'), language::get('errSearchProviderNotFound'));

        if(!isset($_SESSION['search_data']) || empty($_SESSION['search_data']))
            throw new messageException(language::get('error'), language::get('errQueryNotSpecified'));

        $params = $arguments[0];
        $data = unserialize($_SESSION['search_data']);

        $provider = self::$providers[$name];

        $view = new HTMLview('search/results.tpl');
        $view->results = array($provider->getResults($data['query'], $params["page"], array_grep($data, '/^'.$provider->name.'_(.*)$/si')));
        $view->query = $data['query'];
        return $view;
    }
}
?>  