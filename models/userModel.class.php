<?php
class userModel extends dataBaseModel
{
    protected $_predefinedQueries = array(
	'getPassword'	=> 'SELECT `password`, `id`, `banned` FROM `%p%users` WHERE `login` = {1}',
	'userExist'	=> 'SELECT `login` FROM `%p%users` WHERE `login` = {1}',
	'userExistID'	=> 'SELECT `login` FROM `%p%users` WHERE `id` = {1}',
	'mailUsed'	=> 'SELECT `id` FROM `%p%users` WHERE `mail` = {1}',
	'register'	=> 'INSERT INTO `%p%users`(`login`, `password`, `mail`, `fullname`, `sex`, `place`, `desc`, `twitter`, `xmpp`, `gg`, `url`, `groups`, `register_date`, `br_date`, `additional_fields`) VALUES({1}, {2}, {3}, {4}, {5}, {6}, {7}, {8}, {9}, {10}, {11}, {12}, {13}, {14}, {15})',
	'delete'	=> 'DELETE FROM `%p%users` WHERE `id` = {1}',
	'ban'		=> 'UPDATE `%p%users` SET `banned` = \'1\' WHERE `id` = {1}', 
	'unban'		=> 'UPDATE `%p%users` SET `banned` = \'0\' WHERE `id` = {1}',
	'edit'		=> 'UPDATE `%p%users` SET  `mail` = {2}, `fullname` = {3}, `sex` = {4}, `place` = {5}, `desc` = {6}, `twitter` = {7}, `xmpp` = {8}, `gg` = {9}, `url` = {10}, `register_date` = {11}, `br_date` = {12}, `additional_fields` = {13} WHERE `id` = {1}',
	'getLimited'	=> 'SELECT * FROM `%p%users` LIMIT {1}, {2}',
	'getUser'	=> 'SELECT * FROM `%p%users` WHERE `id` = {1}',
	'getLimitedFromGroup' => 'SELECT * FROM `%p%users` WHERE `groups` LIKE \'%|{3}|%\' LIMIT {1}, {2}',
	'getGroup'	=> array('SELECT * FROM `%p%users_groups` WHERE `id` = {1}', 'stdClass'),
	'setGroups'	=> 'UPDATE `%p%users` SET `groups` = {1} WHERE `id` = {2}',
	'updateLastActivity' => 'UPDATE `%p%users` SET `last_activity` = {1} WHERE `id` = {2}',
	'_getAdditionalFields' => array('SELECT * FROM `%p%additional_fields`', 'stdClass'),
	'_addAdditionalField' => array('', 'stdClass'),
	'_updateAddtionalField' => array('', 'stdClass')
	    );
    
    protected $_defaultDAOname = 'user';
    
    public function getUserData($userID)
    {
	$userData = $this->getUser($userID);
	if(!$userData) return false;
        
	$groupIDs = explode(",", $userData->groups);
        
	foreach($groupIDs as $id => $groupID)
	   $groupIDs[$id] = str_replace('|', '', $groupID);
        
	$groupPermissions = array();
	$groups = $this->getGroups($groupIDs);
	$groups = (is_array($groups) ? $groups : array($groups));
	foreach($groups as $group)
	{
	    $a = (isset($group->permission) ? explode(',', $group->permission) : array());
	    $groupPermissions = array_merge($groupPermissions, $a);
	}
	
	$userData->_permissions = array_merge($groupPermissions, (isset($userData->permissions) ? explode(',', $userData->permissions) : array()));
        
        $userData->groups = $groups;
	$userData->additional_fields = unserialize($userData->additional_fields);
	
	return $userData;
    }
    
    public function getUsersCount()
    {
	return self::$connection->executeQuery('SELECT COUNT(*) FROM `%p%users`')->fetchColumn();
    }
    
    public function getUsersCountInGroup($group)
    {
	return self::$connection->executeQuery('SELECT COUNT(*) FROM `%p%users` WHERE `groups` LIKE \'%|'.$group.'|%\'')->fetchColumn();
    }
    
    public function getGroups(array $groups)
    {
	$qL = 'SELECT * FROM `%p%users_groups` WHERE ';
	foreach($groups as $id => $val)
	{
	    $groups[$id] = "`id` = '".$val."'";
	}
	$qL .= implode(' OR ', $groups);
	return $this->proccessSQL($qL, 'stdClass');
    }
    
    public function getAdditionalFields()
    {
	$res = $this->_getAdditionalFields();
        if(!$res) return array();
	if(!is_array($res)) $res = array($res);

	foreach($res as $row => $field)
	{
	    $res[$row]->lang = unserialize($field->lang);
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