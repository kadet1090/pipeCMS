<?php

class pmModel extends dataBaseModel
{
    public static $bindings = array(
        'author'    => 'user',
        'receiver'  => 'user'
    );

    protected $_predefinedQueries = array(
        'getLimitedTo'=> 'SELECT
            `%p%pm`.*,
            `author`.*,
            `%p%users_groups`.`suffix`     AS `author.suffix`,
            `%p%users_groups`.`prefix`     AS `author.prefix`,
            `%p%users_groups`.`color`      AS `author.color`
        FROM `%p%pm`, `%p%users` AS `author`, `%p%users_groups`
        WHERE 
            `author`.`id` = `%p%pm`.`author` AND
            `%p%users_groups`.`id` = `author`.`main_group` AND
            `%p%pm`.`receiver` = :1 
        LIMIT :2, :3',

        'getLimitedFrom'=> 'SELECT
            `%p%pm`.*,
            `receiver`*,
            `%p%users_groups`.`suffix`     AS `receiver.suffix`,
            `%p%users_groups`.`prefix`     AS `receiver.prefix`,
            `%p%users_groups`.`color`      AS `receiver.color`
        FROM `%p%pm`, `%p%users` AS `receiver`, `%p%users_groups`
        WHERE
            `receiver`.`id` = `%p%pm`.`receiver` AND
            `%p%users_groups`.`id` = `receiver`.`main_group` AND
            `%p%pm`.`author` = :1
        LIMIT :2, :3',
        
        'getMessage' => array('SELECT
            `%p%pm`.*,
            `author`.*,
            `%p%users_groups`.`suffix`     AS `author.suffix`,
            `%p%users_groups`.`prefix`     AS `author.prefix`,
            `%p%users_groups`.`color`      AS `author.color`
        FROM `%p%pm`, `%p%users` AS `author`, `%p%users_groups`
        WHERE 
            `author`.`id` = `%p%pm`.`author` AND
            `%p%users_groups`.`id` = `author`.`main_group` AND
            `%p%pm`.`id` = :1', true),
        
        'send' => 'INSERT INTO `%p%pm`(`title`, `content`, `author`, `receiver`, `date`) VALUES(:1, :2, :3, :4, :5)',
        'makeRead' => 'UPDATE `%p%pm` SET `read` = 1 WHERE `id` = :1'
    );
    
    public function getMessagesCount($userID)
    {
        return self::$connection->executeQuery('SELECT COUNT(*) FROM `%p%pm` WHERE `receiver` = \''.$userID.'\'')->fetchColumn();
    }
}
?>
