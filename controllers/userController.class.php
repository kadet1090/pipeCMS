<?php
class userController extends controller
{
    public function login($params = array(), $data = array())
    {
        view::$robots = "none";
        if(!self::$user->isLogged)
        {
            if(isset($data['submit']))
            {
                $model = new userModel();

                if($data['login'] == '') throw new messageException(language::get('error'), language::get('errUsernameNotSet'), array('url' => array('user', 'login')));
                if($data['password'] == '') throw new messageException(language::get('error'), language::get('errPassNotSet'), array('url' => array('user', 'login')));


                $user = $model->getPassword($data['login']);
                if($user)
                {
                    if(pass($data['password']) == $user->password)
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
    
    public function logout($params = array(), $data = array())
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
    
    public function register($params = array(), $data = array())
    {
        view::$robots = "all";
        if(!self::$user->isLogged)
        {
            $captha = new captha();
            $model = new userModel();
            if(isset($data['submit']))
            {
                if($data['login'] == '')  throw new messageException(language::get('error'), language::get('errUsernameNotSet'),  array('url' => array('user', 'register')));
                if($data['mail'] == '')   throw new messageException(language::get('error'), language::get('errMailNotSet'),            array('url' => array('user', 'register')));
                //if($data['fullname'] == '') throw new messageException(language::get('error'), language::get('errFullnameNotSet'),  array('url' => array('user', 'register')));
                if($data['password'] == '') throw new messageException(language::get('error'), language::get('errPasswordNotSet'),  array('url' => array('user', 'register')));
                if($data['repassword'] == '') throw new messageException(language::get('error'), language::get('errRepasswordNotSet'),array('url' => array('user', 'register')));
                //if($data['sex'] == '') throw new messageException(language::get('error'), language::get('errSexNotSet'),            array('url' => array('user', 'register')));
                
                if($data['day'] == ''    || $data['day'] > 31 || $data['day'] < 1)            throw new messageException(language::get('error'), language::get('errWrongBirthDay'),  array('url' => array('user', 'register')));
                if($data['month'] == ''  || $data['month'] > 12 || $data['month'] < 1)  throw new messageException(language::get('error'), language::get('errWrongBirthMonth'),array('url' => array('user', 'register')));
                if($data['year'] == ''   || $data['year'] > date('Y')-9 || $data['year'] < date('Y')-99) throw new messageException(language::get('error'), language::get('errWrongBirthYear'), array('url' => array('user', 'register')));
                if(!preg_match('/^[a-zA-Z0-9\_\-]*$/s', $data['login'])) throw new messageException(language::get('error'), language::get('errBadLogin'),  array('url' => array('user', 'register')));
                if(!preg_match('/^[a-zA-Z0-9\_\-\.]*@[a-z\_\-]*\.[a-z]{2,3}/', $data['mail'])) throw new messageException(language::get('error'), language::get('errWrongMail'), array('url' => array('user', 'register')));
                if($data['repassword'] != $data['password']) throw new messageException(language::get('error'), language::get('errPassNotMatch'), array('url' => array('user', 'register')));
                
                if($model->userExist($data['login'])) throw new messageException(language::get('error'), language::get('errUserAlreadyExist'), array('url' => array('user', 'register')));
                if($model->mailUsed($data['mail'])) throw new messageException(language::get('error'), language::get('errUsedMail'), array('url' => array('user', 'register')));
                
                if(!$captha->check($data["captha"])) throw new messageException(language::get('error'), language::get('capthaNotValid'), array('url' => array('user', 'register')));
                
                /*login, password, mail, fullname, sex, place, desc, twitter, xmpp, gg, url, groups, register_date, br_date, additional_fields*/
                $model->register(
                        $data['login']      , pass($data['password']),
                        $data['mail']       , $data['fullname'],
                        $data['sex']        , $data['place'],
                        $data['desc']       , $data['twitter'],
                        $data['xmpp']       , $data['gg'],
                        $data['url']        , '|'.self::$config->defaultGroupID.'|',
                        date("Y-m-d")                     , $data['year'].'-'.$data['month'].'-'.$data['day'],
                        serialize(array_grep($data, '/^add_(.*)$/si'))
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
    
    public function profile($params = array(), $data = array())
    {
        view::$robots = "none";
        if(self::$user->hasPermission('user/profile'))
        {
            $userModel = new userModel();
            $view = new HTMLview('user/profile.tpl');
            $view->fields = $userModel->getAdditionalFields();
            if(self::$router->match('profile'))
            {
                $user = $userModel->getUserData($params['id']);
                if(!$user) throw new messageException(language::get('error'), language::get('errUserNotExists'));
                $view->user = $user;
                return $view;
            }
            else
                $view->user = self::$user;
            return $view;
        }
        else
            throw new messageException(language::get('error'), language::get('errNoPermission'), array('url' => array('index', 'index')));
    }
    
    public function delete($params = array(), $data = array())
    {
        view::$robots = "none";
        if(self::$router->match('profile'))
        {
            if(self::$user->hasPermission("user/delete"))
            {
                $userModel = new userModel();
                $user = $userModel->delete($params['id']);
                if(!$user) throw new messageException(language::get('success'), language::get('userDeleted'));
            }
            else
                throw new messageException(language::get('error'), language::get('errNoPermission'));
        }
        else throw new messageException(language::get('error'), language::get('errWrongURL'));
    }
    
    public function ban($params = array(), $data = array())
    {
        view::$robots = "none";
        if(self::$router->match('profile'))
        {
            if(self::$user->hasPermission("user/ban"))
            {
                $userModel = new userModel();
                $user = $userModel->ban($params['id']);
                $userModel->updateLastActivity(0, $params['id']);
                if(!$user) throw new messageException(language::get('success'), language::get('userBanned'));
            }
            else
                throw new messageException(language::get('error'), language::get('errNoPermission'));
        }
        else throw new messageException(language::get('error'), language::get('errWrongURL'));
    }
    
    public function unban($params = array(), $data = array())
    {
        view::$robots = "none";
        if(self::$router->match('profile'))
        {
            if(self::$user->hasPermission("user/unban"))
            {
                $userModel = new userModel();
                $user = $userModel->unban($params['id']);
                if(!$user) throw new messageException(language::get('success'), language::get('userUnbanned'));
            }
            else
                throw new messageException(language::get('error'), language::get('errNoPermission'));
        }
        else throw new messageException(language::get('error'), language::get('errWrongURL'));
    }
    
    public function edit($params = array(), $data = array())
    {
        view::$robots = "none";
        if(self::$user->isLogged)
        {
            if(isset($data['submit']))
            {
                $model = new userModel();
                
                if($data['mail'] == '')        throw new messageException(language::get('error'), language::get('errMailNotSet'));
                
                if($data['day'] != ''   && $data['day'] > 31 || $data['day'] < 1) throw new messageException(language::get('error'), language::get('errWrongBirthDay'));
                if($data['month'] != '' && $data['month'] > 12 || $data['month'] < 1) throw new messageException(language::get('error'), language::get('errWrongBirthMonth'));
                if($data['year'] != ''  && $data['year'] > date('Y')-9 || $data['year'] < date('Y')-99)throw new messageException(language::get('error'), language::get('errWrongBirthYear'));
                
                if(!isMail($data['mail'])) throw new messageException(language::get('error'), language::get('errWrongMail'));
                $mail = $model->mailUsed($data['mail']);
                if($mail && $mail->id != $params['id'])        throw new messageException(language::get('error'), language::get('errUsedMail'));
                            
                $this->_uploadAvatar($params['login']);
                
                /*login, mail, fullname, sex, place, desc, twitter, xmpp, gg, url, groups, register_date, br_date, additional_fields*/
                $model->edit($params['id'],
                    $data['mail']        , $data['fullname'],
                    $data['sex']         , $data['place'],
                    $data['desc']        , $data['twitter'],
                    $data['xmpp']        , $data['gg'],
                    $data['url']         , date("Y-m-d"), 
                    $data['year'].'-'.$data['month'].'-'.$data['day'],
                    serialize(array_grep($data, '/^add_(.*)$/si'))
                );
                
                throw new messageException(language::get('success'), language::get('userEditSuccess'), array('url' => array('user', 'profile', $params['login'], $params['id'])));
            }
            else
            {
                $userModel = new userModel();
                if(self::$router->match('profile') && $params['id'] != self::$user->id)
                {
                    if(self::$user->hasPermission("user/edit"))
                    {
                        $user = $userModel->getUserData($params['id']);
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
    
    public function addGroup($params = array(), $data = array())
    {
        view::$robots = "none";
        if(self::$user->isLogged)
        {
            $model = new userModel();
            $group = $model->getGroup($params['id']);
            
            if(self::$router->match("category"))
            {
                if($group->type == "open")
                {
                    $user = $model->getUser(self::$user->id);
                    $groups = explode(",", $user->groups);
                    $groups[] = '|'.$params['id'].'|';
                    $model->setGroups(implode(",", array_unique($groups)), self::$user->id);
                }
            }
            elseif(self::$router->match("addGroup"))
            {
                if(self::$user->hasPermission("user/addGroup") || self::$user->id == $group->admin)
                {
                    $user = $model->getUser($params['id']);
                    $groups = explode(",", $user->groups);
                    $groups[] = '|'.$params['id'].'|';
                    $model->setGroups(implode(",", array_unique($groups)), $params['id']);
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
    
    public function removeGroup($params = array(), $data = array())
    {
        view::$robots = "none";
        if(self::$user->isLogged)
        {
            $model = new userModel();
            
            if($params['id'] == self::$config->defaultGroupID)
                throw new messageException(language::get('error'), language::get('errCantRemoveDefaultGroup'));
            
            $group = $model->getGroup($params['id']);
            
            if(self::$router->match("category"))
            {
                $user = self::$user;
            }
            elseif(self::$router->match("addGroup"))
            {
                if(self::$user->hasPermission("user/removeGroup") || self::$user->id == $group->admin)
                    $user = $model->getUser($params['userId']);
                else
                    throw new messageException(language::get('error'), language::get('errNoPermission'));
            }
            else
                throw new messageException(language::get('error'), language::get('errWrongUrl'));
            
            $groups = explode(",", $user->groups);
            unset($groups[array_search('|'.$params['id'].'|', $groups)]);
            $model->setGroups(implode(",", array_unique($groups)), $user->id);
        }
        else
            throw new messageException(language::get('error'), language::get('errNotLoggedIn'), array('url' => array('user', 'login')));
    }
    
    public function setMainGroup($params = array(), $data = array())
    {
        view::$robots = "none";
        if(self::$user->isLogged)
        {
            $model = new userModel();
            
            if(self::$router->match("addGroup"))
            {
                if(self::$user->hasPermission("user/mainGroupChange"))
                {
                    $user = $model->setMainGroup($params['id'], $params['userId']);
                    throw new messageException(language::get('success'), language::get('mainGroupChanged'));
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
    
    public function joinGroup($params = array(), $data = array())     { return $this->addGroup($params = array(), $data = array()); }
    public function leaveGroup($params = array(), $data = array())    { return $this->removeGroup($params = array(), $data = array()); }
    
    private function _uploadAvatar($username)
    {
        try 
        {
            $data = uploader::upload('avatar', 'avatar', $username, true, array('image/png', 'image/jpeg', 'image/gif'));

            switch($data['type'])
            {
                case 'image/png':
                    $image = imagecreatefrompng($data['path']);
                    break;
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($data['path']);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($data['path']);
                    break;
            }
            if(imagesx($image) > 100 || imagesy($image) > 100) throw new messageException(language::get('error'), language::get('errTooBigImg'));

            imagesavealpha($image, true);
            imagepng($image, 'usersData/avatars/'.$username.'.png');
        }
        catch (frameworkException $e)
        {
            switch($e->getCode())
            {
                case uploader::UPLOAD_ERROR:
                    return false;
                    break;
                case uploader::WRONG_TYPE:
                    $msg = language::get('errWrongType');
                    break;
                case uploader::TOO_BIG:
                    $msg = language::get('errTooBigFileSize');
                    break;
            }
            
            throw new messageException(language::get('error'), $msg);
        }
    }
}
?>
