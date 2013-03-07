<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kadet
 * Date: 07.03.13
 * Time: 19:10
 * To change this template use File | Settings | File Templates.
 */
interface pluginInterface
{
    public function install();
    public function uninstall();

    public function init();
}