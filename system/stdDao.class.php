<?php
class stdDao
{
    private $_variables = array();

    public function __set($name, $value)
    {
        $this->_variables[$name] = is_serialized($value) ?
            unserialize($value) :
            $value;
    }

    public function __get($name)
    {
        return isset($this->_variables[$name]) ?
            $this->_variables[$name] :
            null;
    }

    public function __isset($name) {
        return isset($this->_variables[$name]) && !empty($this->_variables[$name]);
    }

    public function __unset($name) {
        if(isset($this->_variables[$name]))
            unset($this->_variables[$name]);
    }
}
