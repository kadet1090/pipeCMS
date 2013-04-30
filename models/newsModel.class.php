<?php
class newsModel extends dataBaseModel
{
    public static $bindings  = array(
        'author' => 'user'
    );

    protected $_predefinedQueries = array(
        'get' => array('SELECT 
                `%p%news`.*,
                `author`.*,
                `%p%users_groups`.`suffix`     AS `author.suffix`,
                `%p%users_groups`.`prefix`     AS `author.prefix`,
                `%p%users_groups`.`color`      AS `author.color`,
                `%p%news_categories`.`name`    AS `category.name`
            FROM `%p%news`, `%p%users` as `author`, `%p%users_groups`, `%p%news_categories`
            WHERE 
                `%p%news_categories`.`id` = `%p%news`.`category` AND 
                `author`.`id` = `%p%news`.`author` AND
                `%p%users_groups`.`id` = `author`.`main_group` AND
                `%p%news`.`id` = :1', true),

        'getLimited' => 'SELECT SQL_CALC_FOUND_ROWS
                `%p%news`.*,
                `%p%users`.`login`             AS `author.login`,
                `%p%users_groups`.`suffix`     AS `author.suffix`,
                `%p%users_groups`.`prefix`     AS `author.prefix`,
                `%p%users_groups`.`color`      AS `author.color`,
                `%p%news_categories`.`name`    AS `category.name`
            FROM `%p%news`, `%p%users`, `%p%users_groups`, `%p%news_categories`
            WHERE 
                `%p%news_categories`.`id` = `%p%news`.`category` AND 
                `%p%users`.`id` = `%p%news`.`author` AND
                `%p%users_groups`.`id` = `%p%users`.`main_group` AND
                `%p%news`.`lang` = :3
                ORDER BY `%p%news`.`id` DESC LIMIT :1, :2',
        
        'getLimitedFromCategory' => 'SELECT SQL_CALC_FOUND_ROWS
                `%p%news`.*,
                `%p%users`.`login`             AS `author.login`,
                `%p%users_groups`.`suffix`     AS `author.suffix`,
                `%p%users_groups`.`prefix`     AS `author.prefix`,
                `%p%users_groups`.`color`      AS `author.color`,
                `%p%news_categories`.`name`    AS `category.name`
            FROM `%p%news`, `%p%users`, `%p%users_groups`, `%p%news_categories`
            WHERE 
                `%p%news_categories`.`id` = `%p%news`.`category` AND 
                `%p%users`.`id` = `%p%news`.`author` AND
                `%p%users_groups`.`id` = `%p%users`.`main_group` AND
                `%p%news`.`lang` = :3 AND 
                `%p%news`.`category` = :4 
            ORDER BY `%p%news`.`id` DESC LIMIT :1, :2',
        
        'add'               => 'INSERT INTO `%p%news`(`title`, `content`, `lang`, `author`, `added`, `category`) VALUES(:1, :2, :3, :4, :5, :6)',
        'edit'              => 'UPDATE `%p%news` SET `title` = :2, `content` = :3, `category` = :4 WHERE `id` = :1',
        'delete'            => 'DELETE FROM `%p%news` WHERE `id` = :1',
        'getCategories'     => 'SELECT * FROM `%p%news_categories`',
        'getCategory'       => array('SELECT * FROM `%p%news_categories` WHERE `id` = :1', true),
        'increaseViews'     => 'UPDATE `%p%news` SET `views` = `views`+1 WHERE `id` = :1',
        'searchLimited'     => 'SELECT SQL_CALC_FOUND_ROWS
                `%p%news`.*,
                `%p%users`.`login`             AS `author.login`,
                `%p%users_groups`.`suffix`     AS `author.suffix`,
                `%p%users_groups`.`prefix`     AS `author.prefix`,
                `%p%users_groups`.`color`      AS `author.color`,
                `%p%news_categories`.`name`    AS `category.name`
            FROM `%p%news`, `%p%users`, `%p%users_groups`, `%p%news_categories`
            WHERE
                `%p%news_categories`.`id` = `%p%news`.`category` AND
                `%p%users`.`id` = `%p%news`.`author` AND
                `%p%users_groups`.`id` = `%p%users`.`main_group` AND
                `%p%news`.`lang` = :2 AND (
                    `%p%news`.`title` LIKE :1 COLLATE utf8_general_ci OR
                    `%p%news`.`content` LIKE :1 COLLATE utf8_general_ci
                )
                ORDER BY `%p%news`.`id` DESC LIMIT :3, :4'
    );

    protected $_validationRules = array(
        'add' => array(
            0 => array(
                'type' => 'callback',
                'func' => 'isEmpty',
                'error' => 'errTitleNotSet',
                'negation' => true
            ),
            1 => array(
                'type' => 'callback',
                'func' => 'isEmpty',
                'error' => 'errContentNotSet',
                'negation' => true
            ),
            2 => array(
                'type' => 'regex',
                'pattern' => '/^[a-zA-Z_]{2,6}$/',
                'error' => 'errWrongLanguageCode',
            ),
            5 => array(
                'type' => 'callback',
                'func' => 'newsModel::categoryExists',
                'error' => 'errWrongCategory',
            )
        ),
        'edit' => array(
            1 => array(
                'type' => 'callback',
                'func' => 'isEmpty',
                'error' => 'errTitleNotSet',
                'negation' => true
            ),
            2 => array(
                'type' => 'callback',
                'func' => 'isEmpty',
                'error' => 'errContentNotSet',
                'negation' => true
            ),
            3 => array(
                'type' => 'callback',
                'func' => 'newsModel::categoryExists',
                'error' => 'errWrongCategory',
            )
        )
    );
    
    public function getNewsCount()
    {
        return self::$connection->executeQuery('SELECT COUNT(*) FROM `%p%news`')->fetchColumn();
    }

    public static function categoryExists($id)
    {
        return (bool)self::$connection->executeQuery('SELECT `id` FROM `%p%news_categories` WHERE `id` = :1', array($id))->fetchColumn();
    }
    
    public function getNewsCountFromCategory($category)
    {
        return self::$connection->executeQuery('SELECT COUNT(*) FROM `%p%news` WHERE `category` = \''.addslashes($category).'\'')->fetchColumn();
    }
}
?>