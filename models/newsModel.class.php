<?php
class newsModel extends dataBaseModel
{
    protected $_predefinedQueries = array(
	'get'		=> 'SELECT *, 
	    (SELECT `name` FROM `%p%news_categories` WHERE `%p%news_categories`.`id` = `%p%news`.`category`) AS `categoryName`,
	    (SELECT `login` FROM `%p%users` WHERE `%p%users`.`id` = `%p%news`.`author`) AS `authorName`
	    FROM `%p%news` WHERE `id` = {1}',
	'getLimited'	=> 'SELECT *, 
	    (SELECT `name` FROM `%p%news_categories` WHERE `%p%news_categories`.`id` = `%p%news`.`category`) AS `categoryName`,
	    (SELECT `login` FROM `%p%users` WHERE `%p%users`.`id` = `%p%news`.`author`) AS `authorName` FROM `%p%news` WHERE `lang` = {3} ORDER BY `id` DESC LIMIT {1}, {2}',
	'getLimitedFromCategory' => 'SELECT *, 
	    (SELECT `name` FROM `%p%news_categories` WHERE `%p%news_categories`.`id` = `%p%news`.`category`) AS `categoryName`,
	    (SELECT `login` FROM `%p%users` WHERE `%p%users`.`id` = `%p%news`.`author`) AS `authorName` FROM `%p%news` WHERE `lang` = {3} AND `category` = {4} ORDER BY `id` DESC LIMIT {1}, {2}',
	'add'		=> 'INSERT INTO `%p%news`(`title`, `content`, `lang`, `author`, `added`, `category`) VALUES({1}, {2}, {3}, {4}, {5}, {6})',
	'edit'		=> 'UPDATE `%p%news` SET `title` = {2}, `content` = {3}, `category` = {4} WHERE `id` = {1}',
	'delete'	=> 'DELETE FROM `%p%news` WHERE `id` = {1}',
	'getCategories' => 'SELECT * FROM `%p%news_categories`',
	'getCategory'	=> 'SELECT * FROM `%p%news_categories` WHERE `id` = {1}',
	'increaseViews'	=> 'UPDATE `%p%news` SET `views` = `views`+1 WHERE `id` = {1}'
    );
    
    public function getNewsCount()
    {
	return self::$connection->executeQuery('SELECT COUNT(*) FROM `%p%news`')->fetchColumn();
    }
    
    public function getNewsCountFromCategory($category)
    {
	return self::$connection->executeQuery('SELECT COUNT(*) FROM `%p%news` WHERE `category` = \''.$category.'\'')->fetchColumn();
    }
}
?>