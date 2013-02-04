<?php
class userController extends controller
{
    public function login()
    {
	view::$robots = "none";
	if(!self::$user->isLogged)
	{
	    if(self::$router->post('submit') != null)
	    {
		$model = new userModel();

		if(self::$router->post('login') == '') throw new messageException(language::get('error'), language::get('errUsernameNotSet'), array('url' => array('user', 'login')));
		if(self::$router->post('password') == '') throw new messageException(language::get('error'), language::get('errPassNotSet'), array('url' => array('user', 'login')));


		$user = $model->getPassword(self::$router->post('login'));
		if($user)
		{
		    if(pass(self::$router->post('password')) == $user->password)
		    {
			if(!$user->banned)
			{
			    $user->isLogged = true;
			    $_SESSION['userid'] = $user->id;
			    self::$user = $model->getUserData($user->id);
			    throw new messageException(language::get('success'), language::get('loginSuccess'), array('url' => array('index', 'index')));
			}
			else	
			    throw new messageException(language::get('error'), language::get('errBanned'), array('url' => array('user', 'login')));
		    }
		    else
		    throw new messageException(language::get('error'), language::get('errWrongPassword'), array('url' => array('user', 'login')));
		}
		else
		    throw new messageException(language::get('error'), language::get('errUserNotExist'), array('url' => array('user', 'login')));
	    }
	    else 
	    {
		$view = new HTMLview( 'user/login-form.tpl');
		return $view;
	    }
	}
	else
	    throw new messageException(language::get('error'), language::get('errAlreadyLoggedIn'), array('url' => array('index', 'index')));
    }
    
    public function logout()
    {
	view::$robots = "none";
	if(self::$user->isLogged)
	{
	    $userModel = new userModel();
	    $userModel->updateLastActivity(0, self::$user->id);
	    session_destroy();
	    self::$user = new user();
	    throw new messageException(language::get('success'), language::get('logoutSuccess'), array('url' => array('index', 'index')));
	}
    }
    
    public function register()
    {
	view::$robots = "all";
	if(!self::$user->isLogged)
	{
	    $captha = new captha();
	    $model = new userModel();
	    if(self::$router->post('submit') != null)
	    {
		var_dump(self::$router->post());
		var_dump(array_grep(self::$router->post(), '/^add_(.*)$/si'));
		if(self::$router->post('login') == '')	throw new messageException(language::get('error'), language::get('errUsernameNotSet'),  array('url' => array('user', 'register')));
		if(self::$router->post('mail') == '') throw new messageException(language::get('error'), language::get('errMailNotSet'),	    array('url' => array('user', 'register')));
		//if(self::$router->post('fullname') == '') throw new messageException(language::get('error'), language::get('errFullnameNotSet'),  array('url' => array('user', 'register')));
		if(self::$router->post('password') == '') throw new messageException(language::get('error'), language::get('errPasswordNotSet'),  array('url' => array('user', 'register')));
		if(self::$router->post('repassword') == '') throw new messageException(language::get('error'), language::get('errRepasswordNotSet'),array('url' => array('user', 'register')));
		//if(self::$router->post('sex') == '') throw new messageException(language::get('error'), language::get('errSexNotSet'),	    array('url' => array('user', 'register')));
		
		if(self::$router->post('day') == ''	|| self::$router->post('day') > 31 || self::$router->post('day') < 1)	    throw new messageException(language::get('error'), language::get('errWrongBirthDay'),  array('url' => array('user', 'register')));
		if(self::$router->post('month') == ''  || self::$router->post('month') > 12 || self::$router->post('month') < 1)  throw new messageException(language::get('error'), language::get('errWrongBirthMonth'),array('url' => array('user', 'register')));
		if(self::$router->post('year') == ''	|| self::$router->post('year') > date('Y')-9 || self::$router->post('year') < date('Y')-99) throw new messageException(language::get('error'), language::get('errWrongBirthYear'), array('url' => array('user', 'register')));
		if(!preg_match('/^[a-zA-Z0-9\_\-]*$/s', self::$router->post('login'))) throw new messageException(language::get('error'), language::get('errBadLogin'),  array('url' => array('user', 'register')));
		if(!preg_match('/^[a-zA-Z0-9\_\-\.]*@[a-z\_\-]*\.[a-z]{2,3}/', self::$router->post('mail'))) throw new messageException(language::get('error'), language::get('errWrongMail'), array('url' => array('user', 'register')));
		if(self::$router->post('repassword') != self::$router->post('password')) throw new messageException(language::get('error'), language::get('errPassNotMatch'), array('url' => array('user', 'register')));
		
		if($model->userExist(self::$router->post('login'))) throw new messageException(language::get('error'), language::get('errUserAlreadyExist'), array('url' => array('user', 'register')));
		if($model->mailUsed(self::$router->post('mail'))) throw new messageException(language::get('error'), language::get('errUsedMail'), array('url' => array('user', 'register')));
		
		if(!$captha->check(self::$router->post("captha"))) throw new messageException(language::get('error'), language::get('capthaNotValid'), array('url' => array('user', 'register')));
		
		/*login, password, mail, fullname, sex, place, desc, twitter, xmpp, gg, url, groups, register_date, br_date, additional_fields*/
		$model->register(
			self::$router->post('login')	, pass(self::$router->post('password')),
			self::$router->post('mail')	, self::$router->post('fullname'),
			self::$router->post('sex')	, self::$router->post('place'),
			self::$router->post('desc')	, self::$router->post('twitter'),
			self::$router->post('xmpp')	, self::$router->post('gg'),
			self::$router->post('url')	, '|'.self::$config->defaultGroupID.'|',
			date("Y-m-d")			, self::$router->post('year').'-'.self::$router->post('month').'-'.self::$router->post('day'),
			serialize(array_grep(self::$router->post(), '/^add_(.*)$/si'))
			);
		
		throw new messageException(language::get('success'), language::get('registerSuccess'), array('url' => array('index', 'index')));
	    }
	    else 
	    {
		$view = new HTMLview( 'user/register-form.tpl');
		$view->captha = $captha->generate();
		$view->fields = $model->getAdditionalFields();
		return $view;
	    }
	}
	else
	    throw new messageException(language::get('error'), language::get('errAlreadyLoggedIn'), array('url' => array('user', 'register')));
    }
    
    public function profile()
    {
	view::$robots = "none";
	if(self::$user->isLogged)
	{
	    $userModel = new userModel();
	    $view = new HTMLview('user/profile.tpl');
	    $view->fields = $userModel->getAdditionalFields();
	    if(self::$router->match('profile'))
	    {
		$user = $userModel->getUserData(self::$router->id);
		if(!$user) throw new messageException(language::get('error'), language::get('errUserNotExists'));
		$view->user = $user;
		return $view;
	    }
	    else
		$view->user = self::$user;
	    return $view;
	}
	else
	    throw new messageException(language::get('error'), language::get('errNotLoggedIn'), array('url' => array('index', 'index')));
    }
    
    public function delete()
    {
	view::$robots = "none";
	if(self::$user->isLogged)
	{
	    if(self::$router->match('profile'))
	    {
		if(self::$user->hasPermission("user/delete"))
		{
		    $userModel = new userModel();
		    $user = $userModel->delete(self::$router->id);
		    if(!$user) throw new messageException(language::get('success'), language::get('userDeleted'));
		}
		else
		    throw new messageException(language::get('error'), language::get('errNoPermission'));
	    }
	    else throw new messageException(language::get('error'), language::get('errWrongURL'));
	}
	else
	    throw new messageException(language::get('error'), language::get('errNotLoggedIn'), array('url' => array('index', 'index')));
    }
    
    public function ban()
    {
	view::$robots = "none";
	if(self::$user->isLogged)
	{
	    if(self::$router->match('profile'))
	    {
		if(self::$user->hasPermission("user/ban"))
		{
		    $userModel = new userModel();
		    $user = $userModel->ban(self::$router->id);
		    $userModel->updateLastActivity(0, self::$router->id);
		    if(!$user) throw new messageException(language::get('success'), language::get('userBanned'));
		}
		else
		    throw new messageException(language::get('error'), language::get('errNoPermission'));
	    }
	    else throw new messageException(language::get('error'), language::get('errWrongURL'));
	}
	else
	    throw new messageException(language::get('error'), language::get('errNotLoggedIn'), array('url' => array('index', 'index')));
    }
    
    public function unban()
    {
	view::$robots = "none";
	if(self::$user->isLogged)
	{
	    if(self::$router->match('profile'))
	    {
		if(self::$user->hasPermission("user/ban"))
		{
		    $userModel = new userModel();
		    $user = $userModel->unban(self::$router->id);
		    if(!$user) throw new messageException(language::get('success'), language::get('userUnbanned'));
		}
		else
		    throw new messageException(language::get('error'), language::get('errNoPermission'));
	    }
	    else throw new messageException(language::get('error'), language::get('errWrongURL'));
	}
	else
	    throw new messageException(language::get('error'), language::get('errNotLoggedIn'), array('url' => array('index', 'index')));
    }
    
    public function edit()
    {
	view::$robots = "none";
	if(self::$user->isLogged)
	{
	    if(self::$router->post('submit'))
	    {
		$model = new userModel();
		
		if(self::$router->post('mail') == '')	throw new messageException(language::get('error'), language::get('errMailNotSet'));
		
		if(self::$router->post('day') != ''   && self::$router->post('day') > 31 || self::$router->post('day') < 1) throw new messageException(language::get('error'), language::get('errWrongBirthDay'));
		if(self::$router->post('month') != '' && self::$router->post('month') > 12 || self::$router->post('month') < 1) throw new messageException(language::get('error'), language::get('errWrongBirthMonth'));
		if(self::$router->post('year') != ''  && self::$router->post('year') > date('Y')-9 || self::$router->post('year') < date('Y')-99)throw new messageException(language::get('error'), language::get('errWrongBirthYear'));
		
		if(!isMail(self::$router->post('mail'))) throw new messageException(language::get('error'), language::get('errWrongMail'));
		$mail = $model->mailUsed(self::$router->post('mail'));
		if($mail && $mail->id != self::$router->id)	throw new messageException(language::get('error'), language::get('errUsedMail'));
			    
		#avatar upload
		if(is_uploaded_file($_FILES['avatar']['tmp_name']))
		{	
		    switch($_FILES['avatar']['type'])
		    {
			case 'image/png':
			    $image = imagecreatefrompng($_FILES['avatar']['tmp_name']);
			    break;
			case 'image/jpeg':
			    $image = imagecreatefromjpeg($_FILES['avatar']['tmp_name']);
			    break;
			case 'image/gif':
			    $image = imagecreatefromgif($_FILES['avatar']['tmp_name']);
			    break;
			default:
			    unlink($_FILES['avatar']['tmp_name']);
			    throw new messageException(language::get('error'), language::get('errBadType'));
			    break;
		    }
		    unlink($_FILES['avatar']['tmp_name']);
		    if($_FILES['avatar']['size'] > 200 * 1024) throw new messageException(language::get('error'), language::get('errTooBigFileSize'));
		    if(imagesx($image) > 100 || imagesy($image) > 100) throw new messageException(language::get('error'), language::get('errTooBigImg'));
		    
		    imagesavealpha($image, true);
		    imagepng($image, 'usersData/avatars/'.self::$router->login.'.png');
		}
		#end of avatar upload
		
		/*login, mail, fullname, sex, place, desc, twitter, xmpp, gg, url, groups, register_date, br_date, additional_fields*/
		$model->edit(self::$router->id,
                    self::$router->post('mail')	, self::$router->post('fullname'),
                    self::$router->post('sex')         , self::$router->post('place'),
                    self::$router->post('desc')	, self::$router->post('twitter'),
                    self::$router->post('xmpp')	, self::$router->post('gg'),
                    self::$router->post('url')         , date("Y-m-d"), 
                    self::$router->post('year').'-'.self::$router->post('month').'-'.self::$router->post('day'),
                    serialize(array_grep(self::$router->post(), '/^add_(.*)$/si'))
                );
		
		throw new messageException(language::get('success'), language::get('userEditSuccess'), array('url' => array('user', 'profile', self::$router->login, self::$router->id)));
	    }
	    else
	    {
		$userModel = new userModel();
		if(self::$router->match('profile') && self::$router->id != self::$user->id)
		{
		    if(self::$user->hasPermission("user/edit"))
		    {
			$user = $userModel->getUserData(self::$router->id);
			if(!$user) throw new messageException(language::get('error'), language::get('errUserNotExists'));

			$view = new HTMLview( 'user/profile-edit.tpl');
			$view->fields = $userModel->getAdditionalFields();
			$view->user = $user;
			return $view;
		    }
		    else
			throw new messageException(language::get('error'), language::get('errNoPermission'));
		}
		else
		{
		    $view = new HTMLview( 'user/profile-edit.tpl');
		    $view->fields = $userModel->getAdditionalFields();
		    $view->user = self::$user;
		    return $view;
		}
	    }
	}
	else
	    throw new messageException(language::get('error'), language::get('errNotLoggedIn'), array('url' => array('user', 'login')));
    }
    
    public function addGroup()
    {
	view::$robots = "none";
	if(self::$user->isLogged)
	{
	    $model = new userModel();
	    $group = $model->getGroup(self::$router->id);
	    
	    if(self::$router->match("category"))
	    {
		if($group->type == "open")
		{
		    $user = $model->getUser(self::$user->id);
		    $groups = explode(",", $user->groups);
		    $groups[] = '|'.self::$router->id.'|';
		    $model->setGroups(implode(",", array_unique($groups)), self::$user->id);
		}
	    }
	    elseif(self::$router->match("addGroup"))
	    {
		if(self::$user->hasPermission("user/addGroup") || self::$user->id == $group->admin)
		{
		    $user = $model->getUser(self::$router->id);
		    $groups = explode(",", $user->groups);
		    $groups[] = '|'.self::$router->id.'|';
		    $model->setGroups(implode(",", array_unique($groups)), self::$router->id);
		}
		else
		    throw new messageException(language::get('error'), language::get('errNoPermission'));
	    }
	    else
		throw new messageException(language::get('error'), language::get('errWrongUrl'));
	}
	else
	    throw new messageException(language::get('error'), language::get('errNotLoggedIn'), array('url' => array('user', 'login')));
    }
    
    public function removeGroup()
    {
	view::$robots = "none";
	if(self::$user->isLogged)
	{
	    $model = new userModel();
	    $group = $model->getGroup(self::$router->id);
	    
	    if(self::$router->match("category"))
	    {
		$user = $model->getUser(self::$user->id);
		$groups = explode(",", $user->groups);
		unset($groups[array_search('|'.self::$router->id.'|', $groups)]);
		$model->setGroups(implode(",", array_unique($groups)), self::$user->id);
	    }
	    elseif(self::$router->match("addGroup"))
	    {
		if(self::$user->hasPermission("user/removeGroup") || self::$user->id == $group->admin)
		{
		    $user = $model->getUser(self::$router->id);
		    $groups = explode(",", $user->groups);
		    unset($groups[array_search('|'.self::$router->id.'|', $groups)]);
		    $model->setGroups(implode(",", array_unique($groups)), self::$router->id);
		}
		else
		    throw new messageException(language::get('error'), language::get('errNoPermission'));
	    }
	    else
		throw new messageException(language::get('error'), language::get('errWrongUrl'));
	}
	else
	    throw new messageException(language::get('error'), language::get('errNotLoggedIn'), array('url' => array('user', 'login')));
    }
    
    public function joinGroup()	    { return $this->addGroup(); }
    public function leaveGroup()    { return $this->removeGroup(); }
    
}
?>