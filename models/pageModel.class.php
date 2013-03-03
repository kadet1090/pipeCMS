<?php

class pageModel extends dataBaseModel
{
    protected $_predefinedQueries = array(
        'getByPublicID' => array('SELECT * FROM `%p%pages` WHERE `publicID` = :1', true)
    );
}
?>
