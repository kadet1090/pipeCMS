<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of messageException
 *
 * @author admin
 */
class messageException extends Exception
{
    protected $_messageTitle;
    protected $_messageContent;
    protected $_nextPage;
    
    public function __construct($messageTitle, $messageContent, $nextPage = array()) 
    {
	$this->_messageTitle = $messageTitle;
	$this->_messageContent = $messageContent;
	$this->_nextPage = $nextPage;
    }
    
    public function getTitle()
    {
	return (string)$this->_messageTitle;
    }
    
    public function getContent()
    {
	return (string)$this->_messageContent;
    }
    
    public function getNextPage()
    {
	return (object)$this->_nextPage;
    }
}

?>
