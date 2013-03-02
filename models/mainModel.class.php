<?php

class mainModel extends dataBaseModel
{
    static protected $menu = null;
    
    protected $_predefinedQueries = array(
        'getQuickContact' => 'SELECT `login`, `mail`, `gg`, `xmpp`, `url`, `twitter` FROM `%p%users` WHERE `id` < 4',
        '_getMenu' => 'SELECT * FROM `%p%menu` WHERE `language` = :1 ORDER BY `pos` ASC'
    );
    
    public function getMenu($id, $language)
    {
        if(self::$menu == null)
            self::$menu = $this->_getMenu($language);
        
        return prepareMenu();
    }
}
?>
