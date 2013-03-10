<?php
class pluginManager
{
    private $_plugins;

    public function loadPlugins() {
        $model = new pluginsModel();

        $plugins = $model->getAll();
        foreach($plugins as $plugin) {
            $this->loadPlugin($plugin);
        }
    }

    public function loadPlugin($plugin) {
        include_once $plugin->dir.'/'.$plugin->file;

        $this->_plugins[$plugin->name] = new $plugin->class($plugin->dir);
        $this->_plugins[$plugin->name]->init($plugin->config);
    }
}
