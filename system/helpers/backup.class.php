<?php
class backup {
    private static $_data = array();
    
    public static function load($id) {
        if(!empty($_SESSION['backup_'.$id])) {
            self::$_data = unserialize($_SESSION['backup_'.$id]);
            
            $_SESSION['backup_'.$id] = null;
        }
    }
    
    public static function get($property, $fallback = null) {
        return isset(self::$_data[$property]) ?
            self::$_data[$property] :
            $fallback;
    }
    
    public static function save($id, $data) {
        $_SESSION['backup_'.$id] = serialize($data);
    }
}

?>
