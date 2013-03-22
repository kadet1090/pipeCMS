<?php
class pluginManager
{
    private $_plugins;
    private $_map;

    public function __construct() {
        $this->_map = new classMap();
    }

    public function loadPlugins() {
        $model = new pluginsModel();

        $plugins = $model->getAll();
        foreach($plugins as $plugin) {
            $this->loadPlugin($plugin);
        }

        $autoloader = new autoloader('./', array(), $this->_map);
        $autoloader->ragister();
    }

    public function loadPlugin($plugin) {
        if(!file_exists($plugin->dir.'/'.$plugin->file))
            return false;

        include_once $plugin->dir.'/'.$plugin->file;

        $this->_plugins[$plugin->name] = new $plugin->class($plugin->dir);
        $this->_plugins[$plugin->name]->init($plugin->config);

        if(!file_exists($plugin->dir.'/.classmap') || DEBUG_MODE) {
            $map = new classMap();
            $map->getMap($plugin->dir.'/');
            $map->saveMapToFile($plugin->dir.'/.classmap');
        }

        $this->_map->loadMapFromFile($plugin->dir.'/.classmap');
    }
}
