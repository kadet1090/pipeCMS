<?php

class mainConfig extends config
{

    public function  __construct($file, $type = null)
    {
        parent::__construct($file, $type);
    }
    /** twórca - tworzy jedyny obiekt tej klasy
      * @access public
      * @param string $file ścieżka do pliku
      * @param int $type typ pliku
      * @return config
      **/
    public static function create($file, $type = null)
    {
        if(self::$_instance === false)
        {
            if(isset($type))
                return new mainConfig($file, $type);
            else
                return new mainConfig($file);
        }
        else
            throw new Exception('Main config is exisit!', 16546);
    }

}
?>
