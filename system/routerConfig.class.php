<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of routerConfig
 *
 * @author Admin
 */
class routerConfig extends config
{
    public function valid()
    {
        if(isset($this->_config->staticParams) && isset($this->_config->regeXParams)) return true;
        else return false;
    }

    public function  __construct($file, $type = null)
    {
        parent::__construct($file, $type);
    }

    public function create($file, $type = null, $valid = true)
    {
        if(self::$_instance === false)
        {
            if(isset($type))
            {
                if($valid == true)
                {
                    if($this->valid()) return new routerConfig($file, $type);
                    else throw new Exception('Config file is not valid!', 16601);
                }
                else return new routerConfig($file, $type);

            }
            else
            {
                if($valid == true)
                {
                    if($this->valid()) return new routerConfig($file, $type);
                    else throw new Exception('Config file is not valid!', 16601);
                }
                else return new routerConfig($file);
            }
        }
        else
        {
            throw new configException('Main config is exisit!', 16546, 'konfiguracja');
        }
    }
}
?>
