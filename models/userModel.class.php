<?php
class userModel extends dataBaseModel
{
    protected $_predefinedQueries = array(
        'getPassword'   => array('SELECT `password`, `id`, `banned` FROM `%p%users` WHERE `login` = :1', true),
        'userExist'     => 'SELECT `login` FROM `%p%users` WHERE `login` = :1',
        'userExistID'   => 'SELECT `login` FROM `%p%users` WHERE `id` = :1',
        'mailUsed'      => array('SELECT `id` FROM `%p%users` WHERE `mail` = :1', true),
        'register'      => 'INSERT INTO `%p%users`(`login`, `password`, `mail`, `fullname`, `sex`, `place`, `desc`, `twitter`, `xmpp`, `gg`, `url`, `groups`, `register_date`, `br_date`, `additional_fields`) VALUES(:1, :2, :3, :4, :5, :6, :7, :8, :9, :10, :11, :12, :13, :14, :15)',
        'delete'        => 'DELETE FROM `%p%users` WHERE `id` = :1',
        'ban'           => 'UPDATE `%p%users` SET `banned` = \'1\' WHERE `id` = :1', 
        'unban'         => 'UPDATE `%p%users` SET `banned` = \'0\' WHERE `id` = :1',
        'edit'          => 'UPDATE `%p%users` SET  `mail` = :2, `fullname` = :3, `sex` = :4, `place` = :5, `desc` = :6, `twitter` = :7, `xmpp` = :8, `gg` = :9, `url` = :10, `register_date` = :11, `br_date` = :12, `additional_fields` = :13 WHERE `id` = :1',
        
        'getLimited'    => 'SELECT SQL_CALC_FOUND_ROWS `%p%users`.*, `%p%users_groups`.`prefix` as `prefix`, `%p%users_groups`.`suffix` as `suffix`, `%p%users_groups`.`color` as `color`
            FROM `%p%users`, `%p%users_groups`
            WHERE `%p%users_groups`.`id` = `%p%users`.`main_group`
            LIMIT :1, :2',

        'getUser'       => array('SELECT `%p%users`.*, `%p%users_groups`.`prefix` as `prefix`, `%p%users_groups`.`suffix` as `suffix`, `%p%users_groups`.`color` as `color`
            FROM `%p%users`, `%p%users_groups`
            WHERE `%p%users`.`id` = :1 AND `%p%users_groups`.`id` = `%p%users`.`main_group`', true),

        'getLimitedFromGroup' => 'SELECT SQL_CALC_FOUND_ROWS `%p%users`.*, `%p%users_groups`.`prefix`, `%p%users_groups`.`suffix`, `%p%users_groups`.`color`, (SELECT COUNT(*) FROM `%p%users` WHERE `groups` LIKE :3) AS `count`
            FROM `%p%users`, `%p%users_groups`
            WHERE `%p%users_groups`.`id` = `%p%users`.`main_group` AND `%p%users`.`groups` LIKE :3
            LIMIT :1, :2',

        'getGroup'      => array('SELECT *, (SELECT COUNT(*) FROM `%p%users` WHERE `groups` LIKE :2) AS `count` FROM `%p%users_groups` WHERE `id` = :1', true, 'stdClass'),
        'setGroups'     => 'UPDATE `%p%users` SET `groups` = :1 WHERE `id` = :2',
        'setMainGroup'  => 'UPDATE `%p%users` SET `main_group` = :1 WHERE `id` = :2',
        'updateLastActivity'    => 'UPDATE `%p%users` SET `last_activity` = :1 WHERE `id` = :2',
        '_getAdditionalFields'  => array('SELECT * FROM `%p%additional_fields`', false, 'stdClass'),
        '_setAdditionalField'   => '',
        'searchLimited' => 'SELECT SQL_CALC_FOUND_ROWS `%p%users`.*, `%p%users_groups`.`prefix`, `%p%users_groups`.`suffix`, `%p%users_groups`.`color`
            FROM `%p%users`, `%p%users_groups`
            WHERE `%p%users_groups`.`id` = `%p%users`.`main_group` AND `%p%users`.`login` LIKE :1
            LIMIT :2, :3'
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
        $res = $this->proccessSQL($SQL, array(), false, 'stdClass');
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
}
?>