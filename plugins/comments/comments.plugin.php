<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kadet
 * Date: 07.03.13
 * Time: 19:42
 * To change this template use File | Settings | File Templates.
 */
class commentsPlugin extends plugin
{
    public function install()
    {

    }

    public function uninstall()
    {

    }

    public function init($config)
    {
        hookManager::add('after_content', array($this, 'afterContent'));
        commentsController::$dir = $this->directory;
    }

    public function afterContent($type, $id, $data) {
        $controller = new commentsController();
        echo $controller->$type(array('id' => $id, 'content' => $data));
    }
}
