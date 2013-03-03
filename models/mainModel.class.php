<?php

class mainModel extends dataBaseModel
{
    protected $_predefinedQueries = array(
        'getMenu' => 'SELECT * FROM `%p%menu` WHERE `language` = :1 ORDER BY `pos` ASC'
    );
}
?>
