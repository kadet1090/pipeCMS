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
            hookManager::add('before_index', 'sliderPlugin::before_index');
        }
    }

    public static function before_index() {
        $view = new HTMLview('slider/slider.tpl', self::$directory.'/template');
        $view->slides = self::$slides;
        echo $view;
    }
}
