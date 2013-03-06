<?php
class autologin 
{
    public static function set($userID, $time) {
        $data = $userID.';';
        $data .= time().';';
        $data .= $_SERVER['REMOTE_ADDR'].';';
        $data .= $_SERVER['HTTP_USER_AGENT'];
        
        cookie::set('autologin', $data, $time);
    }
    
    public static function get() {
        if(!cookie::get('autologin'))
            return false;
        
        $data = cookie::get('autologin');
        $data = explode(';', $data, 4);
        
        if($_SERVER['REMOTE_ADDR'] != $data[2]) return false;
        if($_SERVER['HTTP_USER_AGENT'] != $data[3]) return false;
        
        return $data[0];
    }
    
    public static function off() {
        cookie::remove('autologin');
    }
}

?>
