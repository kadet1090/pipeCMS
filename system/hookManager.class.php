<?php
/**
 * @author Kadet <kadet1090@gmail.com>
 */
class hookManager
{
    private static $_hooks = array();

    public static function run($name, $params = array())
    {
        if(!isset(self::$_hooks[$name]))
            return false;

        $result = array();
        foreach(self::$_hooks[$name] as $hook) {
            $result[] = call_user_func_array($hook, $params);
        }

        return $result;
    }

    public static function add($name, $func)
    {
        self::$_hooks[$name][] = $func;
    }

    public static function remove($name, $function) {
        if(!isset(self::$_hooks[$name]))
            return false;

        $index = array_search($function, self::$_hooks[$name]);
        if($index !== false)
            unset(self::$_hooks[$name][$index]);
    }
}