<?php
class stdDao
{
    public function __set($name, $value)
    {
        if(strpos($name, '.') !== false) {
            $var = strstr($name, '.', true);
            $prop = substr(strstr($name, '.'), 1);

            if(is_numeric($this->$var)) {
                $id = $this->$var;
                $this->$var = new stdDao();
                $this->$var->id = $id;
            }

            if(!isset($this->$var))
                $this->$var = new stdDao();

            $this->$var->$prop = $value;

            return;
        }

        $this->$name = is_serialized($value) ?
            unserialize($value) :
            $value;
    }
}
