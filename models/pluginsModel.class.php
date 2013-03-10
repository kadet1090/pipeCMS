<?php
class pluginsModel extends dataBaseModel
{
    protected $_predefinedQueries = array(
        'getByName' => array('SELECT * FROM `%p%plugins` WHERE `name` = :1', true),
        'getByID' => array('SELECT * FROM `%p%plugins` WHERE `id` = :1', true),
        'getAll' => 'SELECT * FROM `%p%plugins`',
        'register' => 'INSERT INTO `%p%plugins`(`name`, `dir`, `class`, `file`, `config`) VALUES (:1, :2, :3, :4, :5)',
        'unregister' => 'DELETE FROM `%p%plugins` WHERE `id` = :1'
    );
}
