<?php
class user
{
    protected $_varibles = array();
    public $isLogged = false;
    
    public function __get($name)
    {
	return (isset($this->_varibles[$name]) ? $this->_varibles[$name] : null);
    }
    
    public function __set($name, $mValue)
    {
	$this->_varibles[$name] = $mValue;
    }
    
    public function hasPermission($permission)
    {
	if($this->isLogged && (in_array($permission, $this->_permissions) || in_array('*', $this->_permissions) || in_array(current(explode('/', $permission)).'/*', $this->_permissions))) return true;
	return false;
    }
    
    public function getAvatar()
    {
	return file_exists('usersData/avatars/'.$this->login.'.png') ? 'usersData/avatars/'.$this->login.'.png' : 'usersData/avatars/default.png'; 
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
