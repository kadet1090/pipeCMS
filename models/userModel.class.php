<?php
class userModel extends dataBaseModel
{
    protected $_predefinedQueries = array(
        'getPassword'   => array('SELECT `password`, `id`, `banned` FROM `%p%users` WHERE `login` = :1', true),
        'userExist'     => 'SELECT `login` FROM `%p%users` WHERE `login` = :1',
        'userExistID'   => 'SELECT `login` FROM `%p%users` WHERE `id` = :1',
        'mailUsed'      => array('SELECT `id` FROM `%p%users` WHERE `mail` = :1', true),
        'register'      => 'INSERT INTO `%p%users`(`login`, `password`, `mail`, `fullname`, `sex`, `place`, `desc`, `twitter`, `xmpp`, `gg`, `url`, `groups`, `main_group`, `register_date`, `br_date`, `additional_fields`) VALUES(:1, :2, :3, :4, :5, :6, :7, :8, :9, :10, :11, :12, :13, :14, :15, :16)',
        'delete'        => 'DELETE FROM `%p%users` WHERE `id` = :1',
        'ban'           => 'UPDATE `%p%users` SET `banned` = \'1\' WHERE `id` = :1', 
        'unban'         => 'UPDATE `%p%users` SET `banned` = \'0\' WHERE `id` = :1',
        'edit'          => 'UPDATE `%p%users` SET  `mail` = :2, `fullname` = :3, `sex` = :4, `place` = :5, `desc` = :6, `twitter` = :7, `xmpp` = :8, `gg` = :9, `url` = :10, `br_date` = :11, `additional_fields` = :12 WHERE `id` = :1',
        
        'getLimited'    => 'SELECT SQL_CALC_FOUND_ROWS
                `%p%users`.*,
                `%p%users_groups`.`prefix` as `self.prefix`,
                `%p%users_groups`.`suffix` as `self.suffix`,
                `%p%users_groups`.`color`  as `self.color`
            FROM `%p%users`, `%p%users_groups`
            WHERE `%p%users_groups`.`id` = `%p%users`.`main_group`
            LIMIT :1, :2',

        'getUser'       => array('SELECT
                `%p%users`.*,
                `%p%users_groups`.`prefix` as `self.prefix`,
                `%p%users_groups`.`suffix` as `self.suffix`,
                `%p%users_groups`.`color`  as `self.color`
            FROM `%p%users`, `%p%users_groups`
            WHERE
                `%p%users`.`id` = :1 AND
                `%p%users_groups`.`id` = `%p%users`.`main_group`', true),

        'getLimitedFromGroup' => 'SELECT SQL_CALC_FOUND_ROWS
                `%p%users`.*,
                `%p%users_groups`.`prefix` as `self.prefix`,
                `%p%users_groups`.`suffix` as `self.suffix`,
                `%p%users_groups`.`color`  as `self.color`
                (SELECT COUNT(*) FROM `%p%users` WHERE `groups` LIKE :3) AS `count`
            FROM `%p%users`, `%p%users_groups`
            WHERE
                `%p%users_groups`.`id` = `%p%users`.`main_group` AND
                `%p%users`.`groups` LIKE :3
            LIMIT :1, :2',

        'getGroup'      => array('SELECT *, (SELECT COUNT(*) FROM `%p%users` WHERE `groups` LIKE :2) AS `count` FROM `%p%users_groups` WHERE `id` = :1', true, 'stdDao'),
        'setGroups'     => 'UPDATE `%p%users` SET `groups` = :1 WHERE `id` = :2',
        'setMainGroup'  => 'UPDATE `%p%users` SET `main_group` = :1 WHERE `id` = :2',
        'updateLastActivity'    => 'UPDATE `%p%users` SET `last_activity` = :1 WHERE `id` = :2',
        '_getAdditionalFields'  => array('SELECT * FROM `%p%additional_fields`', false, 'stdDao'),
        '_setAdditionalField'   => '',
        'searchLimited' => 'SELECT SQL_CALC_FOUND_ROWS
                `%p%users`.*,
                `%p%users_groups`.`prefix` as `self.prefix`,
                `%p%users_groups`.`suffix` as `self.suffix`,
                `%p%users_groups`.`color`  as `self.color`
            FROM `%p%users`, `%p%users_groups`
            WHERE `%p%users_groups`.`id` = `%p%users`.`main_group` AND (
                `%p%users`.`login` LIKE :1 COLLATE utf8_general_ci OR
                `%p%users`.`mail` LIKE :1 COLLATE utf8_general_ci OR
                `%p%users`.`desc` LIKE :1 COLLATE utf8_general_ci OR
                `%p%users`.`fullname` LIKE :1 COLLATE utf8_general_ci
            )
            LIMIT :2, :3'
    );

    protected $_validationRules = array(
        'register' => array(
            0 => array(
                array(
                    'type' => 'regex',
                    'pattern' => '/^[a-zA-Z0-9\_\-]*$/s',
                    'error' => 'errWrongLogin',
                ),
                array(
                    'type' => 'callback',
                    'func' => 'userModel::userExists',
                    'error' => 'errLoginAlreadyUsed',
                    'negation' => true
                )
            ),
            2 => array(
                array(
                    'type' => 'callback',
                    'func' => 'isMail',
                    'error' => 'errWrongMail',
                ),
                array(
                    'type' => 'callback',
                    'func' => 'userModel::mailUsed',
                    'error' => 'errMailAlreadyUsed',
                    'negation' => true
                )
            ),
            14 => array(
                'type' => 'callback',
                'func' => 'isValidDate',
                'error' => 'errWrongBirthDate'
            )
        ),
        'edit' => array(
            1 => array(
                array(
                    'type' => 'callback',
                    'func' => 'isMail',
                    'error' => 'errWrongMail',
                ),
                array(
                    'type' => 'callback',
                    'func' => 'userModel::mailUsed',
                    'error' => 'errMailAlreadyUsed',
                    'params' => array('value', 0),
                    'negation' => true
                )
            ),
            10 => array(
                'type' => 'callback',
                'func' => 'isValidDate',
                'error' => 'errWrongBirthDate'
            )
        )
    );
    
    protected $_defaultDAOname = 'user';
    
    public function getUserData($userID)
    {
        # get user data and if user not exists return false
        $userData = $this->getUser($userID);
        if(!$userData) return false;
        
        $groupIDs = explode(",", str_replace('|', '', $userData->groups));
        $groups = $this->getGroups($groupIDs);
        
        $userData->_permissions = array();
        if($groups)
            foreach($groups as $group)
                $userData->_permissions = getPermissions($group->permissions, $userData->_permissions);
        
        $userData->_permissions = getPermissions($userData->permissions, $userData->_permissions);
        
        $userData->groups = $groups;
        
        return $userData;
    }
    
    public function getUsersCount()
    {
        return self::$connection->executeQuery('SELECT COUNT(*) FROM `%p%users`')->fetchColumn();
    }
    
    public function getUsersCountInGroup($group)
    {
        return self::$connection->executeQuery('SELECT COUNT(*) FROM `%p%users` WHERE `groups` LIKE \'%|'.addslashes($group).'|%\'')->fetchColumn();
    }
    
    public function getGroups(array $groups)
    {
        $SQL = 'SELECT * FROM `%p%users_groups` WHERE ';
        foreach($groups as $id => $val)
        {
            $groups[$id] = "`id` = '".addslashes($val)."'";
        }
        $SQL .= implode(' OR ', $groups);
        $res = $this->proccessSQL($SQL, array(), false, 'stdDao');
        $ret = array();
        foreach($res as $group) {
            $ret[$group->id] = $group;
        }
        
        return $ret;
    }
    
    public function getAdditionalFields()
    {
        $res = $this->_getAdditionalFields();
        if(!$res) return array();

        foreach($res as $row => $field)
        {
            if($field->type == 'enum' || $field->type == 'radio' || $field->type == 'multi')
            {
                $res[$row]->value = array_combine(explode(',', preg_replace('/[^a-zA-Z0-9\_\-\.\,]/s', '', $field->value)), explode(', ', $field->value));
                $res[$row]->default = preg_replace('/[^a-zA-Z0-9\_\-\.\,]/s', '', $field->default);
            }
        }
        return $res;
    }

    public static function userExists($id) {
        return (bool)self::$connection->executeQuery('SELECT `id` FROM `%p%users` WHERE `id` = :1', array($id))->fetchObject();
    }

    public static function mailUsed($mail, $exclude = -1) {
        $id = self::$connection->executeQuery('SELECT `id` FROM `%p%users` WHERE `mail` = :1', array($mail))->fetchColumn();
        return (!empty($id) && $exclude != $id);
    }
}