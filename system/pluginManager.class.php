<?php
class pluginManager
{
    private $_plugins;

    public function loadPlugins($dir = './plugins/') {
        $map = new classMap();
        $plugins = $map->loadMapFromFile('./cfg/plugins.txt');

        $pluginLoader = new autoloader('./', array(), $map);
        $pluginLoader->ragister();

        foreach($plugins as $plugin => $path) {
            $this->loadPlugin(str_replace('Plugin', '', $plugin), $path);
        }
    }

    public function loadPlugin($name, $path) {
        $class = $name.'Plugin';

        $this->_plugins[$name] = new $class(dirname($path));
        $this->_plugins[$name]->init();
    }
}
