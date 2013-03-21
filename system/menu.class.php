<?php
/**
 * Description of menu
 *
 * @author Kadet <kadet1090@gmail.com>
 */
class menu {
    public static $lang = null;
    private static $_menu = null;
    private static $_cache = array();
    
    public static function get($id) {
        if(!isset(self::$_cache[$id])) {
            if(self::$_menu == null)
            {
                $model = new mainModel();
                self::$_menu = $model->getMenu(self::$lang);
            }



            $ret = array();
            foreach(self::$_menu as $no => $menu) {
                if($menu->menuID == $id && controller::$user->hasPermission($menu->permission)) {
                    $ret[] = $menu;
                    unset(self::$_menu[$no]);
                }
            }
            self::$_cache[$id] = self::_prepareMenu($ret);
        }
        return self::$_cache[$id];
    }
    
    private static function _prepareMenu($menu, $parent = NULL, $self = NULL)
    {
        $return = array();
        foreach($menu as $element) 
            if($element->parent == $parent)
                $return[(int)$element->pos] = self::_prepareMenu($menu, $element->id, $element);

        if($self != NULL) $return['self'] = $self;

        ksort($return);
        return $return;
    }
}

?>
