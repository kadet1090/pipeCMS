<?php
class indexController extends controller
{
    public function index($params = array(), $data = array())
    {
        $view = new HTMLview('index.tpl');
        return $view;
    }
}
?>  