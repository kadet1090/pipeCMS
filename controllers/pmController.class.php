<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of pmController
 *
 * @author admin
 */
class pmController extends controller
{
    public function index($params = array(), $data = array())
    {
        return $this->page(1);
    }
    
    public function page($params = array(), $data = array())
    {
        view::addTitleChunk(language::get("privateMessages"));
        view::$robots = "nofollow";
        if(self::$router->match("page") || !empty($page))
        {
            if(self::$user->isLogged)
            {
                if(empty($page)) $page = $params['page'];
                if($page > 1)
                {
                   view::addTitleChunk(language::get('page'));
                   view::addTitleChunk($page);
                }
                
                $model = new pmModel();
                
                $messages = $model->getLimitedTo(self::$user->id, ($page - 1)  * (int)self::$config->privateMessagesPerPage, (int)self::$config->privateMessagesPerPage);
                $view = new HTMLview("pm/list.tpl");
                $messagesCount = $model->getMessagesCount(self::$user->id);
                if($messages)
                {
                    $view->messages = (is_array($messages) ? $messages : array($messages)); //...wartości
                    $view->pages = helperController::pageList($messagesCount, (int)self::$config->privateMessagesPerPage, $page, array("pm","page"));
                }
                else
                    $view->messages = language::get("errNoPrivateMessages");
                return $view;
            }
            else
                throw new messageException(language::get("error"), language::get("errNotLoggedIn"));
        }
        else
            throw new messageException(language::get("error"), language::get("errWrongUrl"));
    }
    
    public function show($params = array(), $data = array())
    {
        view::$robots = "nofollow";
        if(self::$router->match("pm"))
        {
            if(self::$user->isLogged)
            {
                $model = new pmModel();
                $message = $model->getMessage($params['id']);
                if($message)
                {
                    if($message->receiver == self::$user->id)
                    {
                        $view = new HTMLview("pm/pm.tpl");
                        $view->message = $message;
                        return $view;
                    }
                    else
                        throw new messageException(language::get("error"), language::get("errBadMessageReceiver"));
                }
                else
                    throw new messageException(language::get("error"), language::get("errMessageNotExist"));
            }
            else
                throw new messageException(language::get("error"), language::get("errNotLoggedIn"));
        }
        else
            throw new messageException(language::get("error"), language::get("errWrongUrl"));
    }
    
    
    public function compose($params = array(), $data = array())
    {
        if(self::$user->isLogged)
        {
            if(!isset($data["submit"]))
            {
                $view = new HTMLview("pm/compose.tpl");
                if(self::$router->match("profile"))
                {
                    $model = new userModel();
                    $view->user = $model->getUser($params['id']);
                }
                return $view;
            }
            else
            {
                $model = new userModel();
                if(!isset($data['title']))            throw new messageException(language::get('error'), language::get('errTitleNotSet'));
                if(!isset($data['receiver']))            throw new messageException(language::get('error'), language::get('errReceiverNotSet'));
                if(!isset($data['content']))            throw new messageException(language::get('error'), language::get('errContentNotSet'));
                if(!preg_match("#[1-9][0-9]*#", $data['receiver'])) throw new messageException(language::get('error'), language::get('errBadReceiverID'));
                if(self::$user->id == (int)$data['receiver']) throw new messageException(language::get('error'), language::get('errMsgToYourself'));
                if(!$model->userExistID($data['receiver'])) throw new messageException(language::get('error'), language::get('errUserNotExist'));
                
                $model = new pmModel();
                $model->send($data['title'], $data['content'], self::$user->id, (int)$data['receiver'], time());
                
                throw new messageException(language::get("success"), language::get("pwSendSuccess"));
            }
        }
        else
            throw new messageException(language::get("error"), language::get("errNotLoggedIn"));
    }
    
    public function reply($params = array(), $data = array())
    {
        if(self::$user->isLogged)
        {
            if(self::$router->match("profile"))
            {
                $model = new pmModel();
                $userModel = new userModel();
                $message = $model->getMessage($params['id']);
                
                if($message)
                {
                    $view = new HTMLview("pm/compose.tpl");
                    $view->user = $userModel->getUser($params['id']);
                    $view->title = "RE: ".$message->title;
                    return $view;
                }
                else 
                    throw new messageException(language::get("error"), language::get("errNotLoggedIn"));
            }
            else 
                throw new messageException(language::get("error"), language::get("errWrongUrl"));
        }
        else 
            throw new messageException(language::get("error"), language::get("errNotLoggedIn"));
    }
}

?>
