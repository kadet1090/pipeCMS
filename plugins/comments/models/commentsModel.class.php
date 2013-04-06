<?php

class commentsModel extends dataBaseModel
{
    public static $bindings  = array(
        'author' => 'user'
    );

    protected $_predefinedQueries = array(
        'getComments' => 'SELECT SQL_CALC_FOUND_ROWS
            `%p%comments`.*,
            `author`.*,
            `%p%users_groups`.`suffix`     AS `author.suffix`,
            `%p%users_groups`.`prefix`     AS `author.prefix`,
            `%p%users_groups`.`color`      AS `author.color`
        FROM `%p%comments`, `%p%users` as `author`, `%p%users_groups`
        WHERE
            `author`.`id` = `%p%comments`.`author` AND
            `%p%users_groups`.`id` = `author`.`main_group` AND
            `%p%comments`.`content_id` = :1 AND
            `%p%comments`.`content_type` = :2
        ORDER BY `%p%comments`.`id` ASC',

        'get' => array('SELECT
            `%p%comments`.*,
            `author`.*,
            `%p%users_groups`.`suffix`     AS `author.suffix`,
            `%p%users_groups`.`prefix`     AS `author.prefix`,
            `%p%users_groups`.`color`      AS `author.color`
        FROM `%p%comments`, `%p%users` as `author`, `%p%users_groups`
        WHERE
            `author`.`id` = `%p%comments`.`author` AND
            `%p%users_groups`.`id` = `author`.`main_group` AND
            `%p%comments`.`id` = :1', true),
        'add'       => 'INSERT INTO `%p%comments`(`content_type`, `content_id`, `content`, `author`, `date`) VALUES(:1, :2, :3, :4, :5)',
        'delete'    => 'DELETE FROM `%p%comments` WHERE `id` = :1',
        'edit'      => 'UPDATE `%p%comments` SET `content` = :2 WHERE `id` = :1'
    );
}
