<?php

class pageModel extends dataBaseModel
{
    protected $_predefinedQueries = array(
        'getByPublicID' => 'SELECT * FROM `%p%pages` WHERE `publicID` = {1}'
    );
}
?>
