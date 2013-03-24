<?php
class user extends stdDao
{
    public $isLogged = false;
    public $id = 0;

    public function hasPermission($permission)
    {
        if(empty($permission))
            return true;
        
        if(!$this->isLogged)
            return false;
        
        if(strstr($permission, '/') == false)
            return isset($this->_permissions[$permission]) && array_search(true, $this->_permissions[$permission]) !== false;
        
        $cat = strstr($permission, '/', true);
        $perm = substr(strstr($permission, '/'), 1);
        
        if(isset($this->_permissions[$cat][$perm]))
            return $this->_permissions[$cat][$perm];
        elseif(isset($this->_permissions[$cat]['all']))
            return $this->_permissions[$cat]['all'];
        elseif(isset($this->_permissions['all']['all']))
            return $this->_permissions['all']['all'];
        else return false;
    }
    
    public function getAvatar()
    {
        if(file_exists('data/avatar/'.$this->login.'.png'))
            return 'data/avatar/'.$this->login.'.png';
        else
            return 'http://www.gravatar.com/avatar/'.md5(strtolower(trim($this->mail))).'?d=identicon&s=100';
    }
    
    public function isOnline()
    {
        return time() - $this->last_activity < 360;
    }
    
    public function isAdmin()
    {
        return $this->hasPermission('acp');
    }
    
    public function inGroup($id) {
        return isset($this->groups[$id]);
    }
}
?>
