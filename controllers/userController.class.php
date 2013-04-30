<?php
class userController extends controller
{
    // TODO: Rewrite that shit :3
    public function login($params = array(), $data = array())
    {
        view::$robots = "none";
        if(self::$user->isLogged) throw new messageException(language::get('error'), language::get('errAlreadyLoggedIn'), array('url' => array('index', 'index')));

        if(isset($data['submit']))
        {
            $model = new userModel();

            if($data['login'] == '') throw new messageException(language::get('error'), language::get('errUsernameNotSet'), array('url' => array('user', 'login')));
            if($data['password'] == '') throw new messageException(language::get('error'), language::get('errPassNotSet'), array('url' => array('user', 'login')));
            $user = $model->getPassword($data['login']);

            if(!$user) throw new messageException(language::get('error'), language::get('errUserNotExist'), array('url' => array('user', 'login')));
            if(pass($data['password']) != $user->password) throw new messageException(language::get('error'), language::get('errWrongPassword'), array('url' => array('user', 'login')));
            if($user->banned) throw new messageException(language::get('error'), language::get('errBanned'), array('url' => array('user', 'login')));

            $_SESSION['userid'] = $user->id;
            self::$user = $model->getUserData($user->id);
            self::$user->isLogged = true;

            if(isset($data['remember']) && $data['remember'])
                autologin::set($user->id, 60 * 60 * 24 * 30);

            return self::message(language::get('success'), language::get('loginSuccess'), array('url' => array('index', 'index')));
        }
        else
        {
            $view = new HTMLview( 'user/login-form.tpl');
            return $view;
        }
    }
    
    public function logout($params = array(), $data = array())
    {
        view::$robots = "none";
        if(!self::$user->isLogged) throw new messageException(__('error'), __('errNotLoggedIn'));

        $userModel = new userModel();
        $userModel->updateLastActivity(0, self::$user->id);

        session_destroy();
        self::$user = new user();

        autologin::off();

        return self::message(language::get('success'), language::get('logoutSuccess'), array('url' => array('index', 'index')));
    }
    
    public function register($params = array(), $data = array())
    {
        view::$robots = "all";
        if(self::$user->isLogged) throw new messageException(language::get('error'), language::get('errAlreadyLoggedIn'), array('url' => array('user', 'register')));

        $captha = new captha();
        $model = new userModel();

        if(isset($data['submit']))
        {
            backup::save('user_register', $data);


            if(empty($data['password'])) throw new messageException(language::get('error'), language::get('errPasswordNotSet'));
            if($data['repassword'] != $data['password']) throw new messageException(language::get('error'), language::get('errPassNotMatch'));
            if(!$captha->check($data["captha"])) throw new messageException(language::get('error'), language::get('capthaNotValid'));

            $model->register(
                $data['login']      , pass($data['password']),
                $data['mail']       , $data['fullname'],
                $data['sex']        , $data['place'],
                $data['desc']       , $data['twitter'],
                $data['xmpp']       , $data['gg'],
                $data['url']        ,
                '|'.self::$config->defaultGroupID.'|', self::$config->defaultGroupID,
                date("Y-m-d")       , $data['year'].'-'.$data['month'].'-'.$data['day'],
                array_grep($data, '/^add_(.*)$/si')
            );

            return self::message(language::get('success'), language::get('registerSuccess'), array('url' => array('index', 'index')));
        }
        else
        {
            $view = new HTMLview( 'user/register-form.tpl');
            $view->captha = $captha->generate();
            $view->fields = $model->getAdditionalFields();

            backup::load('user_register');
            return $view;
        }
    }
    
    public function profile($params = array(), $data = array())
    {
        view::$robots = "none";
        if(!self::$user->hasPermission('user/profile')) throw new messageException(language::get('error'), language::get('errNoPermission'), array('url' => array('index', 'index')));

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
    
    public function delete($params = array(), $data = array())
    {
        view::$robots = "none";
        if(!self::$router->match('profile')) throw new messageException(language::get('error'), language::get('errWrongURL'));
        if(self::$user->hasPermission("user/delete")) throw new messageException(language::get('error'), language::get('errNoPermission'));

        $userModel = new userModel();
        $userModel->delete($params['id']);

        return self::message(language::get('success'), language::get('userDeleted'));
    }
    
    public function ban($params = array(), $data = array())
    {
        view::$robots = "none";
        if(!self::$router->match('profile')) throw new messageException(language::get('error'), language::get('errWrongURL'));
        if(!self::$user->hasPermission("user/ban")) throw new messageException(language::get('error'), language::get('errNoPermission'));

        $userModel = new userModel();
        $userModel->ban($params['id']);
        $userModel->updateLastActivity(0, $params['id']);

        return self::message(language::get('success'), language::get('userBanned'));
    }
    
    public function unban($params = array(), $data = array())
    {
        view::$robots = "none";
        if(!self::$router->match('profile')) throw new messageException(language::get('error'), language::get('errWrongURL'));
        if(!self::$user->hasPermission("user/unban")) throw new messageException(language::get('error'), language::get('errNoPermission'));

        $userModel = new userModel();
        $userModel->unban($params['id']);
        return self::message(language::get('success'), language::get('userUnbanned'));
    }
    
    public function edit($params = array(), $data = array())
    {
        view::$robots = "none";
        if(!self::$user->isLogged) throw new messageException(language::get('error'), language::get('errNotLoggedIn'), array('url' => array('user', 'login')));

        if(isset($data['submit']))
        {
            $model = new userModel();
            $model->edit($params['id'],
                $data['mail']        , $data['fullname'],
                $data['sex']         , $data['place'],
                $data['desc']        , $data['twitter'],
                $data['xmpp']        , $data['gg'],
                $data['url']         ,
                $data['year'].'-'.$data['month'].'-'.$data['day'],
                array_grep($data, '/^add_(.*)$/si')
            );
            $this->_uploadAvatar($params['login']);

            return self::message(language::get('success'), language::get('userEditSuccess'), array('url' => array('user', 'profile', $params['login'], $params['id'])));
        }
        else
        {
            $userModel = new userModel();
            if(self::$router->match('profile') && $params['id'] != self::$user->id)
            {
                if(!self::$user->hasPermission("user/edit")) throw new messageException(language::get('error'), language::get('errNoPermission'));

                $user = $userModel->getUserData($params['id']);
                if(!$user) throw new messageException(language::get('error'), language::get('errUserNotExists'));

                $view = new HTMLview( 'user/profile-edit.tpl');
                $view->fields = $userModel->getAdditionalFields();
                $view->user = $user;
                return $view;
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
    
    public function addGroup($params = array(), $data = array())
    {
        view::$robots = "none";
        
        if(!self::$user->isLogged) throw new messageException(language::get('error'), language::get('errNotLoggedIn'), array('url' => array('user', 'login')));

        $model = new userModel();

        $group = $model->getGroup($params['id'], '%|'.$params['id'].'|%');

        if(self::$router->match("category"))
            $user = self::$user;
        elseif(self::$router->match("addGroup"))
        {
            if(!self::$user->hasPermission("user/addGroup") && self::$user->id != $group->admin) throw new messageException(language::get('error'), language::get('errNoPermission'));

            $user = $model->getUserData($params['userId']);
        }
        else
            throw new messageException(language::get('error'), language::get('errWrongUrl'));

        if(isset($user->groups[$group->id]))
            throw new messageException(language::get('error'), language::get('errUserAlreadyInGroup'));

        $groups = array_keys($user->groups);
        $groups[] = $group->id;

        $model->setGroups('|'.implode("|,|", array_unique($groups)).'|', $user->id);

        if($user->main_group == $group->id)
            $model->setMainGroup($params['id'], $params['userId']);

        return self::message(language::get('success'), language::get('userAddedToGroup'));
    }
    
    public function removeGroup($params = array(), $data = array())
    {
        view::$robots = "none";
        
        if(!self::$user->isLogged) throw new messageException(language::get('error'), language::get('errNotLoggedIn'), array('url' => array('user', 'login')));

        $model = new userModel();
            
        if($params['id'] == self::$config->defaultGroupID)
            throw new messageException(language::get('error'), language::get('errCantRemoveDefaultGroup'));

        $group = $model->getGroup($params['id'], '%|'.$params['id'].'|%');

        if(self::$router->match("category"))
        {
            $user = self::$user;
        }
        elseif(self::$router->match("addGroup"))
        {
            if(self::$user->hasPermission("user/removeGroup") || self::$user->id == $group->admin)
                $user = $model->getUserData($params['userId']);
            else
                throw new messageException(language::get('error'), language::get('errNoPermission'));
        }
        else
            throw new messageException(language::get('error'), language::get('errWrongUrl'));

        if(!isset($user->groups[$group->id]))
            throw new messageException(language::get('error'), language::get('errUserNotInGroup'));
        $groups = $user->groups;
        unset($groups[$group->id]);

        $model->setGroups('|'.implode("|,|", array_unique(array_keys($groups))).'|', $user->id);

        if($user->main_group == $group->id) {
            $model->setMainGroup(self::$config->defaultGroupID, $params['userId']);
        }

        return self::message(language::get('success'), language::get('userRemovedFromGroup'));
    }
    
    public function setMainGroup($params = array(), $data = array())
    {
        view::$robots = "none";
        if(!self::$user->isLogged) throw new messageException(language::get('error'), language::get('errNotLoggedIn'), array('url' => array('user', 'login')));
        if(!self::$router->match("addGroup")) throw new messageException(language::get('error'), language::get('errWrongUrl'));
        if(!self::$user->hasPermission("user/mainGroupChange")) throw new messageException(language::get('error'), language::get('errNoPermission'));

        $model = new userModel();
        $model->setMainGroup($params['id'], $params['userId']);

        return self::message(language::get('success'), language::get('mainGroupChanged'));
    }
    
    public function joinGroup($params = array(), $data = array())     { return $this->addGroup($params, $data); }
    public function leaveGroup($params = array(), $data = array())    { return $this->removeGroup($params, $data); }
    
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
