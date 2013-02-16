<?php
class dataBaseModel extends model
{
    /** Object of DB connection
      * @access protected
      * @var dataBaseConnection 
      **/
    public static $connection;
    
    protected $_predefinedQueries = array(
    );
    
    public function processResult(PDOStatement $result, $oname = '')
    {
        if(empty($oname))
            $oname = $this->_defaultDAOname;
        if(!class_exists($oname))
            $oname = 'stdClass';
        
        $res = array();
        if(preg_match('/^[^A-Z_]*SELECT[^A-Z_]/i', $result->queryString))
        {
            if($result->rowCount() > 1)
            {
                while($row = $result->fetchObject($oname))
                    $res[] = $row;
            }
            else
                $res = $result->fetchObject($oname);

            $this->_data[] = $res;
            return $res;
        }
    }
    
    public function proccessSQL($qL, $oname = '')
    {
        if(empty($oname))
            $oname = $this->_defaultDAOname;
        
        if($query = self::$connection->executeQuery($qL))
        {
            return $this->processResult($query, $oname);
        }
    }
    
    protected function executePredefinedQuery($name, $arguments = array())
    {
        if(is_array($this->_predefinedQueries[$name])) $query = $this->_predefinedQueries[$name][0];
        else $query = $this->_predefinedQueries[$name];
        
        foreach($arguments as $argID => $argument)
        {
            if(is_int($argument)) 
            {
                $query = str_replace('{'.($argID+1).'}', $argument, $query);
                $query = str_replace('['.($argID+1).']', $argument, $query);
            }
            else
            {
                $query = str_replace('{'.($argID+1).'}', '\''.addslashes($argument).'\'', $query);
                $query = str_replace('['.($argID+1).']', addslashes($argument), $query);
            }
        }

        return self::$connection->executeQuery($query);
    }
    
    public function __call($name, $arguments)
    {
        return $this->processResult($this->executePredefinedQuery($name, $arguments), (isset($this->_predefinedQueries[$name][1]) && is_array($this->_predefinedQueries[$name]) ? $this->_predefinedQueries[$name][1] : ''));
    }
}
?>
