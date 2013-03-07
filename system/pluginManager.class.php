<?php
class pluginManager
{
    private $_plugins;

    public function loadPlugins($dir = './plugins/') {
        $map = new classMap();
        $plugins = array_keys($map->loadMapFromFile('./cfg/plugins.txt'));

        $pluginLoader = new autoloader('./', array(), $map);
        $pluginLoader->ragister();

        foreach($plugins as $plugin) {
            $this->loadPlugin(str_replace('Plugin', '', $plugin));
        }
    }

    public function loadPlugin($name) {
        $class = $name.'Plugin';

        $this->_plugins[$name] = new $class;
        $this->_plugins[$name]->init();
    }
}
