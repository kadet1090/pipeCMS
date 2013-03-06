<?php
/**
 * @author Kadet <kadet1090@gmail.com>
 */
class cookie {
    public static $pass = "fnw308n3408n";
    private static $_decrypted = array();
    
    public static function set($name, $content, $time = 0, $path = null) {
        self::$_decrypted[$name] = $content;
        
        $content = self::_encrypt($content, self::$pass);
        setcookie($name, $content, time() + $time, $path);
        $_COOKIE[$name] = $content;
    }
    
    public static function get($name) {
        if(!isset(self::$_decrypted[$name]))
            self::$_decrypted[$name] = (isset($_COOKIE[$name]) ? self::_decrypt($_COOKIE[$name], self::$pass) : null);
        
        return self::$_decrypted[$name];
    }
    
    public static function remove($name) {
        setcookie($name, "", 1);
    }
    
    private static function _encrypt($content, $key) {
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $content, MCRYPT_MODE_CBC, md5(md5($key))));
    }
    
    private static function _decrypt($content, $key) {
        return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($content), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
    }
}

?>
