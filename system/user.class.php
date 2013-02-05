<?php
class user
{
    protected $_varibles = array();
    public $isLogged = false;
    
    public function __get($name)
    {
	return (isset($this->_varibles[$name]) ? $this->_varibles[$name] : null);
    }
    
    public function __isset($name)
    {
	return isset($this->_varibles[$name]) && !empty($this->_varibles[$name]);
    }
    
    public function __set($name, $mValue)
    {
	$this->_varibles[$name] = $mValue;
    }
    
    public function hasPermission($permission)
    {
	if(!$this->isLogged)
            return false;
        
        if(strstr($permission, '/') == false)
            return isset($this->_permissions[$permission]) && $this->_permissions['all'];
        
        $cat = strstr($permission, '/', true);
        $perm = substr(strstr($permission, '/'), 1);
        
        if(isset($this->_permission[$cat][$perm]))
            return $this->_permission[$cat][$perm];
        elseif(isset($this->_permission[$cat]['all']))
            return $this->_permission[$cat]['all'];
        elseif(isset($this->_permissions['all']['all']))
            return $this->_permissions['all']['all'];
        else return false;
    }
    
    public function getAvatar()
    {
        if(file_exists('usersData/avatars/'.$this->login.'.png'))
            return 'usersData/avatars/'.$this->login.'.png';
        else
            return 'http://www.gravatar.com/avatar/'.md5(strtolower(trim($this->mail))).'?d=identicon&s=100';
    }
    
    public function isOnline()
    {
	return time() - $this->last_activity < 360;
    }
    
    public function isAdmin()
    {
	return time() - $this->last_activity < 360;
    }
}
?>
