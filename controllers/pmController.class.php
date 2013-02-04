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
    public function index()
    {
	return $this->page(1);
    }
    
    public function page($page = null)
    {
	view::addTitleChunk(language::get("privateMessages"));
	view::$robots = "nofollow";
	if(self::$router->match("page") || !empty($page))
	{
	    if(self::$user->isLogged)
	    {
		if(empty($page)) $page = self::$router->page;
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
    
    public function show()
    {
	view::$robots = "nofollow";
	if(self::$router->match("pm"))
	{
	    if(self::$user->isLogged)
	    {
		$model = new pmModel();
		$message = $model->getMessage(self::$router->id);
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
    
    
    public function compose()
    {
	if(self::$user->isLogged)
	{
	    if(self::$router->post("submit") == null)
	    {
		$view = new HTMLview("pm/compose.tpl");
		if(self::$router->match("profile"))
		{
		    $model = new userModel();
		    $view->user = $model->getUser(self::$router->id);
		}
		return $view;
	    }
	    else
	    {
		$model = new userModel();
		if(self::$router->post('title') == null)	    throw new messageException(language::get('error'), language::get('errTitleNotSet'));
		if(self::$router->post('receiver') == null)	    throw new messageException(language::get('error'), language::get('errReceiverNotSet'));
		if(self::$router->post('content') == null)	    throw new messageException(language::get('error'), language::get('errContentNotSet'));
		if(!preg_match("#[1-9][0-9]*#", self::$router->post('receiver'))) throw new messageException(language::get('error'), language::get('errBadReceiverID'));
		if(self::$user->id == (int)self::$router->post('receiver')) throw new messageException(language::get('error'), language::get('errMsgToYourself'));
		if(!$model->userExistID(self::$router->post('receiver'))) throw new messageException(language::get('error'), language::get('errUserNotExist'));
		
		$model = new pmModel();
		$model->send(self::$router->post('title'), self::$router->post('content'), self::$user->id, (int)self::$router->post('receiver'), time());
		
		throw new messageException(language::get("success"), language::get("pwSendSuccess"));
	    }
	}
	else
	    throw new messageException(language::get("error"), language::get("errNotLoggedIn"));
    }
    
    public function reply()
    {
	if(self::$user->isLogged)
	{
	    if(self::$router->match("profile"))
	    {
		$model = new pmModel();
		$userModel = new userModel();
		$message = $model->getMessage(self::$router->id);
		
		if($message)
		{
		    $view = new HTMLview("pm/compose.tpl");
		    $view->user = $userModel->getUser(self::$router->id);
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
