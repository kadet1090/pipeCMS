<?php
class stdDao
{
    protected $_columns = array();
    protected $_bindings = array();
    protected $_main = "";
    protected $_column = 0;

    protected $_vars = array();

    /**
     * @param array $columns
     * @param string $model
     */
    public function __construct($columns = array(), $model = "model") {
        $this->_columns = $columns;
        $this->_main = reset($columns);
        $this->_main = $this->_main['table'];

        $this->_bindings = $model::$bindings;
    }

    public function __set($name, $value)
    {
        if(isset($this->_columns[$this->_column]) && getExecutor() == "PDOStatement") {
            $columnData = $this->_columns[$this->_column];

            if($columnData['name'] != $name) {
                $this->_column++;
                $columnData = $this->_columns[$this->_column];
            }

            if($columnData['table'] != $this->_main && strpos($name, '.') === false) {
                $name = $columnData['table'].'.'.$name;
            }

            $this->_column++;
        }
        $name = str_replace('self.', '', $name);

        if(strpos($name, '.') !== false) {

            $var = strstr($name, '.', true);
            $prop = substr(strstr($name, '.'), 1);

            $dao = isset($this->_bindings[$var]) ? $this->_bindings[$var] : 'stdDao';

            if(is_numeric($this->_vars[$var])) {
                $id = $this->_vars[$var];
                $this->_vars[$var] = new $dao();
                $this->_vars[$var]->id = $id;
            }

            if(!isset($this->$var))
                $this->$var = new $dao();

            $this->$var->$prop = $value;

            return;
        }

        $this->_vars[$name] = is_serialized($value) ?
            unserialize($value) :
            $value;
    }

    public function __get($name) {
        return $this->_vars[$name];
    }

    public function __isset($name) {
        return isset($this->_vars[$name]);
    }

    public function __unset($name) {
        unset($this->_vars[$name]);
    }
}
