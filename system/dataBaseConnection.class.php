<?php
class dataBaseConnection
{
    /** PDO object
      * @access protected
      * @var PDO 
      **/
    protected $_PDO;
    
    /** DB user name
      * @access protected
      * @var string 
      **/
    protected $_userName;
    
    /** DB password of specifed user
      * @access protected
      * @var string 
      **/
    protected $_password;
    
    /** DB url \n example 'mysql:dbname=script;host=localhost'
      * @access protected
      * @var string 
      **/
    protected $_dSN;
    
    /** is connected ?
      * @access protected
      * @var bool 
      **/
    protected $_isConnected;
    
    /** a config file object
      * @access protected
      * @var config 
      */
    protected $_config;
    
    /**
     *
     * @var log
     */
    public static $log;

    public static $ns = 0;
    
    public $prefix;
    
    /** constructor
      * @access public
      * @param string $n
      * @param string $userName
      * @param string $password
      **/
    public function  __construct($n = '', $userName = '', $password = '')
    {
        $this->_userName = $userName;
        $this->_n = $n;
        $this->_password = $password;
    }
    
    /** Connecting with db specifed by class vars
      * @access public
      * @param array $options 
      **/
    public function connect($options = array())
    {
        try 
        {
            foreach($this->_config->option as $option)
                $options[constant('PDO::'.$option['name'])] = $option['value'];
            $this->_PDO = new PDO($this->_n, $this->_userName, $this->_password, $options);
            foreach($this->_config->attribute as $attribute)
                $this->setPDOattribute(constant('PDO::'.$attribute['name']), constant('PDO::'.$attribute['value']));
            
            $this->_isConnected = true;
        }
        catch(PDOException $pDOexception)
        {
            echo "test";
            //self::$log->addToLog('('.$exception->getCode().') '.$exception->getMessage()." : ".$exception->getFile()."[".$exception->getLine()."]", "connectionError");
            $this->_isConnected = false;
            /* TODO: show connection error */ print_r($pDOexception);
        }
    }
    
    public function loadConfig(config $config)
    {
        try
        {
            $this->_config = $config;
            if(isset($this->_config->prefix)) $this->prefix = $this->_config->prefix;
            if(isset($this->_config->database->userName)) $this->_userName = $this->_config->database->userName;
            if(isset($this->_config->database->password)) $this->_password = $this->_config->database->password;
            if(isset($this->_config->database->type)) $this->_n = $this->_config->database->type.':';
            if(isset($this->_config->database->name)) $this->_n .= 'dbname='.$this->_config->database->name.';';
            if(isset($this->_config->database->port)) $this->_n .= 'port='.$this->_config->database->port.';';
            if(isset($this->_config->database->userName)) $this->_userName = $this->_config->database->userName;
            $this->_n .= 'host='.(isset($this->_config->database->host) ? $this->_config->database->host : 'localhost');
        }
        catch(Exception $exception)
        {
            self::$log->addToLog('('.$exception->getCode().') '.$exception->getMessage()." : ".$exception->getFile()."[".$exception->getLine()."]", "connectionError");
            echo $e;
        }    
    }
    
    /** is connected ?
      * @access public
      * @return bool 
      */
    public function isConnected()
    {
        return $this->_isConnected;
    }
    
    #gettery
    public function getDSN()
    {
        return $this->_n;
    }
    
    public function getUserName()
    {
        return $this->_userName;
    }
    
    public function getPassword()
    {
        return $this->_password;
    }
    
    #settery
    public function setDSN($n)
    {
        if(!$this->isConnected())
            $this->_n = $n;
    }
    
    public function setUserName($userName)
    {
        if(!$this->isConnected())
            $this->_userName = $userName;
    }
    
    public function setPassword($password)
    {
        if(!$this->isConnected())
            $this->_password = $password;
    }
    
    public function setPDOattribute($attribute, $mValue)
    {
        $this->_PDO->setAttribute($attribute, $mValue);
    }
    
    public function executeQuery($SQL, $arguments = array())
    {
        try
        {
            if(DEBUG_MODE) self::$log->addToLog(trim(str_replace("%p%", $this->prefix, $SQL)), "sql");
            $prepared = $this->_PDO->prepare(trim(str_replace("%p%", $this->prefix, $SQL)));
            
            foreach($arguments as $num => $value) {
                if(is_numeric($value)) 
                    $prepared->bindValue(':'.($num+1), $value, PDO::PARAM_INT);
                elseif(is_array($value))
                    $prepared->bindValue(':'.($num+1), serialize($value));
                else
                    $prepared->bindValue(':'.($num+1), $value);
            }
            
            $prepared->execute();

            self::$ns++;
            return $prepared;
        }
        catch(PDOException $exception)
        {
            self::$log->addToLog('('.$exception->getCode().') '.$exception->getMessage()." : ".$exception->getFile()."[".$exception->getLine()."]", "error");
            throw $exception;
        }
    }
}
?>
