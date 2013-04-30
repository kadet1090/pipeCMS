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
    protected $_defaultDAOname = 'stdDao';
    protected $_validationRules = array();

    public static $bindings = array();

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

    public function isValid($type, $data)
    {
        if (!isset($this->_validationRules[$type])) return true;
        foreach ($this->_validationRules[$type] as $no => $rule) {
            if(array_depth($rule) > 1) {
                $rules = $rule;
                foreach($rules as $rule)
                    $this->_checkData($rule, $data, $no);
            }
            else
                $this->_checkData($rule, $data, $no);
        }
        return true;
    }

    private function _checkData($rule, $data, $field) {
        if(!isset($rule['negation'])) $rule['negation'] = false;
        switch($rule['type']) {
            case 'regex':
                if(preg_match($rule['pattern'], $data[$field]) == $rule['negation'])
                    throw new invalidDataException($field, isset($rule['error']) ? $rule['error'] : 'errFieldInvalid');
                break;

            case 'callback':
                if(isset($rule['params'])) {
                    $params = $rule['params'];
                    array_walk($params, function(&$element) use($data, $field) {
                        if($element === 'value') $element = $data[$field];
                        if(is_numeric($element)) $element = $data[$element];
                    });
                } else {
                    $params = array($data[$field]);
                }

                if(call_user_func_array($rule['func'], $params) == $rule['negation'])
                    throw new invalidDataException($field, isset($rule['error']) ? $rule['error'] : 'errFieldInvalid');
                break;

            default:
                throw new frameworkException('Unimplemented validation rule type.', 2000);
        }
    }
}
