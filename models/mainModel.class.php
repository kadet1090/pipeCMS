<?php

class mainModel extends dataBaseModel
{
    protected $_predefinedQueries = array(
	'getQuickContact' => 'SELECT `login`, `mail`, `gg`, `xmpp`, `url`, `twitter` FROM `%p%users` WHERE `id` < 4',
	'_getMenu' => 'SELECT * FROM `%p%menu` WHERE `language` = {1} ORDER BY `pos` ASC'
    );
    
    public function getMenu($language)
    {
	return prepareMenu($this->_getMenu($language));
    }
}
?>
