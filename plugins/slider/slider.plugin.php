<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kadet
 * Date: 07.03.13
 * Time: 19:42
 * To change this template use File | Settings | File Templates.
 */
class sliderPlugin extends plugin
{
    public static $slides;

    public function install()
    {

    }

    public function uninstall()
    {

    }

    public function init($config)
    {
        if(isset($config["slides"]))
        {
            self::$slides = $config["slides"];
            hookManager::add('before_index', array($this, 'beforeIndex'));
        }
    }

    public function beforeIndex() {
        $view = new HTMLview('slider/slider.tpl', $this->directory.'/template');
        $view->slides = self::$slides;
        echo $view;
    }
}
