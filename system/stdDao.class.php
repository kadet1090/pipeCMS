<?php
class stdDao
{
    public function __set($name, $value)
    {
        $this->$name = is_serialized($value) ?
            unserialize($value) :
            $value;
    }
}
