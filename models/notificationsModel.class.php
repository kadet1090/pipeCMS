<?php

class notificationsModel extends dataBaseModel
{
    protected $_predefinedQueries = array(
        'getNotifications'          => 'SELECT SQL_CALC_FOUND_ROWS *
            FROM `%p%notifications`
            WHERE `receiver` = :1
            ORDER BY `id` DESC
            LIMIT :2, :3',
        'getImportantNotifications' => 'SELECT SQL_CALC_FOUND_ROWS *
        FROM `%p%notifications`
        WHERE
            `receiver` = :1 AND
            `priority` > 7 AND
            `read` = 0
        ORDER BY `id` DESC',
        'setRead' => 'UPDATE `%p%notifications` SET `read` = 1 WHERE `id` = :1',
        'push' => 'INSERT INTO `%p%notifications`(`receiver`, `message`, `type`, `date`, `priority`) VALUES(:1, :2, :3, :4, :5)',
    );

    public function getNotificationsCount($receiver)
    {
        return self::$connection->executeQuery('SELECT COUNT(*) FROM `%p%notifications` WHERE `receiver` = :1 AND `read` = 0', array($receiver))->fetchColumn();
    }
}
?>
