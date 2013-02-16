<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of model
 *
 * @author Admin
 */
abstract class model
{
    protected $_data = array();
    protected $_defaultDAOname = 'stdClass';
    
    public function __get($name)
    {
        return (isset($this->_data[$name]) ? $this->_data[$name] : null);
    }
    
    public function clearAll()
    {
        $this->_data = array();
    }
    
    public function deleteEntry($name)
    {
        $this->_data[$name] = null;
    }
    
    public function getData()
    {
        return $this->_data;
    }
    
    abstract function __call($name, $arguments);
}
?>
