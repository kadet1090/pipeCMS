<?php
class newsModel extends dataBaseModel
{
    protected $_predefinedQueries = array(
        'get'                => 'SELECT 
                `%p%news`.*,
                `%p%users`.`login`             AS `authorName`,
                `%p%users_groups`.`suffix`     AS `suffix`,
                `%p%users_groups`.`prefix`     AS `prefix`,
                `%p%users_groups`.`color`      AS `color`,
                `%p%news_categories`.`name`    AS `categoryName`
            FROM `%p%news`, `%p%users`, `%p%users_groups`, `%p%news_categories`
            WHERE 
                `%p%news_categories`.`id` = `%p%news`.`category` AND 
                `%p%users`.`id` = `%p%news`.`author` AND
                `%p%users_groups`.`id` = `%p%users`.`main_group` AND
                `%p%news`.`id` = {1}',
        
        'getLimited' => 'SELECT 
                `%p%news`.*,
                `%p%users`.`login`             AS `authorName`,
                `%p%users_groups`.`suffix`     AS `suffix`,
                `%p%users_groups`.`prefix`     AS `prefix`,
                `%p%users_groups`.`color`      AS `color`,
                `%p%news_categories`.`name`    AS `categoryName`
            FROM `%p%news`, `%p%users`, `%p%users_groups`, `%p%news_categories`
            WHERE 
                `%p%news_categories`.`id` = `%p%news`.`category` AND 
                `%p%users`.`id` = `%p%news`.`author` AND
                `%p%users_groups`.`id` = `%p%users`.`main_group` AND
                `%p%news`.`lang` = {3}
                ORDER BY `%p%news`.`id` DESC LIMIT {1}, {2}',
        
        'getLimitedFromCategory' => 'SELECT 
                `%p%news`.*,
                `%p%users`.`login`             AS `authorName`,
                `%p%users_groups`.`suffix`     AS `suffix`,
                `%p%users_groups`.`prefix`     AS `prefix`,
                `%p%users_groups`.`color`      AS `color`,
                `%p%news_categories`.`name`    AS `categoryName`
            FROM `%p%news`, `%p%users`, `%p%users_groups`, `%p%news_categories`
            WHERE 
                `%p%news_categories`.`id` = `%p%news`.`category` AND 
                `%p%users`.`id` = `%p%news`.`author` AND
                `%p%users_groups`.`id` = `%p%users`.`main_group` AND
                `%p%news`.`lang` = {3} AND 
                `%p%news`.`category` = {4} 
            ORDER BY `%p%news`.`id` DESC LIMIT {1}, {2}',
        
        'add'                => 'INSERT INTO `%p%news`(`title`, `content`, `lang`, `author`, `added`, `category`) VALUES({1}, {2}, {3}, {4}, {5}, {6})',
        'edit'                => 'UPDATE `%p%news` SET `title` = {2}, `content` = {3}, `category` = {4} WHERE `id` = {1}',
        'delete'        => 'DELETE FROM `%p%news` WHERE `id` = {1}',
        'getCategories' => 'SELECT * FROM `%p%news_categories`',
        'getCategory'        => 'SELECT * FROM `%p%news_categories` WHERE `id` = {1}',
        'increaseViews'        => 'UPDATE `%p%news` SET `views` = `views`+1 WHERE `id` = {1}'
    );
    
    public function getNewsCount()
    {
        return self::$connection->executeQuery('SELECT COUNT(*) FROM `%p%news`')->fetchColumn();
    }
    
    public function getNewsCountFromCategory($category)
    {
        return self::$connection->executeQuery('SELECT COUNT(*) FROM `%p%news` WHERE `category` = \''.addslashes($category).'\'')->fetchColumn();
    }
}
?>