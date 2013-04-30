<?php
/**
 * Created by JetBrains PhpStorm.
 *
 * @author Kadet <kadet1090@gmail.com>
 * @package
 * @license WTFPL
 */

class invalidDataException extends Exception
{
    protected $_field;

    public function __construct($field, $message = "errFieldInvalid", $code = 0, Exception $previous = null)
    {
        $this->field = $field;
        parent::__construct($message, $code, $previous);
    }

    public function getField() {
        return $this->_field;
    }
}