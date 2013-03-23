<?php

class pmModel extends dataBaseModel
{
    protected $_predefinedQueries = array(
        'getLimitedTo'=> 'SELECT
            `%p%pm`.*,
            `%p%users`.`login`             AS `author_login`,
            `%p%users_groups`.`suffix`     AS `author_suffix`,
            `%p%users_groups`.`prefix`     AS `author_prefix`,
            `%p%users_groups`.`color`      AS `author_color`
        FROM `%p%pm`, `%p%users`, `%p%users_groups`
        WHERE 
            `%p%users`.`id` = `%p%pm`.`author` AND
            `%p%users_groups`.`id` = `%p%users`.`main_group` AND 
            `%p%pm`.`receiver` = :1 
        LIMIT :2, :3',

        'getLimitedFrom'=> 'SELECT
            `%p%pm`.*,
            `%p%users`.`login`             AS `receiver_login`,
            `%p%users_groups`.`suffix`     AS `receiver_suffix`,
            `%p%users_groups`.`prefix`     AS `receiver_prefix`,
            `%p%users_groups`.`color`      AS `receiver_color`
        FROM `%p%pm`, `%p%users`, `%p%users_groups`
        WHERE
            `%p%users`.`id` = `%p%pm`.`receiver` AND
            `%p%users_groups`.`id` = `%p%users`.`main_group` AND
            `%p%pm`.`author` = :1
        LIMIT :2, :3',
        
        'getMessage' => array('SELECT
            `%p%pm`.*,
            `%p%users`.`login`             AS `author_login`,
            `%p%users_groups`.`suffix`     AS `author_suffix`,
            `%p%users_groups`.`prefix`     AS `author_prefix`,
            `%p%users_groups`.`color`      AS `author_color`
        FROM `%p%pm`, `%p%users`, `%p%users_groups`
        WHERE 
            `%p%users`.`id` = `%p%pm`.`author` AND
            `%p%users_groups`.`id` = `%p%users`.`main_group` AND 
            `%p%pm`.`id` = :1', true),
        
        'send' => 'INSERT INTO `%p%pm`(`title`, `content`, `author`, `receiver`, `date`) VALUES(:1, :2, :3, :4, :5)'
    );
    
    public function getMessagesCount($userID)
    {
        return self::$connection->executeQuery('SELECT COUNT(*) FROM `%p%pm` WHERE `receiver` = \''.$userID.'\'')->fetchColumn();
    }
}
?>
