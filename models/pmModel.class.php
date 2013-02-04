<?php

class pmModel extends dataBaseModel
{
    protected $_predefinedQueries = array(
	'getLimitedTo'	=> 'SELECT *, (SELECT `login` FROM `%p%users` WHERE `%p%users`.`id` = `%p%pm`.`author`) AS `authorLogin` FROM `%p%pm` WHERE `receiver` = {1} LIMIT {2}, {3}',
	'getMessage'	=> 'SELECT *, (SELECT `login` FROM `%p%users` WHERE `%p%users`.`id` = `%p%pm`.`author`) AS `authorLogin` FROM `%p%pm` WHERE `id` = {1}',
	'send'		=> 'INSERT INTO `%p%pm`(`title`, `content`, `author`, `receiver`, `date`) VALUES({1}, {2}, {3}, {4}, {5})'
    );
    
    public function getMessagesCount($userID)
    {
	return self::$connection->executeQuery('SELECT COUNT(*) FROM `%p%pm` WHERE `receiver` = \''.$userID.'\'')->fetchColumn();
    }
}
?>
