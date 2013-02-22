<?php
/**
 * Cache helper
 * @package Helper
 * @author Kadet <kadet1090@gmail.com>
 * @copyright DO NOT REMOVE THIS COMMAND
 */
class cache {
    public static $cacheDirectory = './data/cache/';
    public static $maxAge = 172800;
    
    /**
     * Caches given data.
     * @param string $module    Module name eg. menu
     * @param string $name      Cached file name.
     * @param string $content   Content to cache.
     */
    public static function set($module, $name, $content)
    {
        $moduleDir = self::$cacheDirectory.$module.'/';
        
        if(!file_exists($moduleDir))
            mkdir($moduleDir, 0777, true);
        
        file_put_contents($moduleDir.$name.'.cache', $content);
    }
    
    /**
     * Gets cached data.
     * @param string $module    Module name eg. menu
     * @param string $name      Cached file name.
     * @return boolean|string
     */
    public static function get($module, $name)
    {
        if(!self::available($module, $name))
            return false;
        
        return file_get_contents(self::$cacheDirectory.$module.'/'.$name.'.cache');
    }
    
    /**
     * Checks availibity of cached data.
     * @param string $module    Module name eg. menu
     * @param string $name      Cached file name.
     * @return boolean
     */
    public static function available($module, $name)
    {
        $file = self::$cacheDirectory.$module.'/'.$name.'.cache';
        
        if(!file_exists($file))
            return false;
        
        if(time() - filemtime($file) > self::$maxAge)
        {
            unlink($file);
            return false;
        }
        
        return true;
    }
}

?>
