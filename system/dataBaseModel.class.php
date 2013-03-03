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
    
    public function processResult(PDOStatement $result, $object = false, $DAOname = '')
    {
        if(empty($DAOname))
            $oname = $this->_defaultDAOname;
        if(!class_exists($DAOname))
            $oname = 'stdClass';
        
        $res = array();
        if(preg_match('/^[^A-Z_]*SELECT[^A-Z_]/i', $result->queryString))
        {
            if($result->rowCount() > 1 || !$object)
            {
                while($row = $result->fetchObject($DAOname))
                $res[] = $row;
            }
            else
                $res = $result->fetchObject($DAOname);

            $this->_data[] = $res;
            return $res;
        }
    }
    
    public function proccessSQL($SQL, $arguments = array(), $object = false, $DAOname = '')
    {
        if(empty($DAOname))
            $DAOname = $this->_defaultDAOname;
        
        if($query = self::$connection->executeQuery($SQL, $arguments))
        {
            return $this->processResult($query, $object, $DAOname);
        }
    }
    
    protected function executePredefinedQuery($name, $arguments = array())
    {
        if(is_array($this->_predefinedQueries[$name])) $query = $this->_predefinedQueries[$name][0];
        else $query = $this->_predefinedQueries[$name];
        
        foreach($arguments as $argID => $argument)
        {
            if(is_int($argument))
                $query = str_replace('['.($argID+1).']', $argument, $query);
            else
                $query = str_replace('['.($argID+1).']', addslashes($argument), $query);
        }
        
        return $this->proccessSQL(
                $query, 
                $arguments, 
                (isset($this->_predefinedQueries[$name][1]) && is_array($this->_predefinedQueries[$name]) ? $this->_predefinedQueries[$name][1] : false),
                (isset($this->_predefinedQueries[$name][2]) && is_array($this->_predefinedQueries[$name]) ? $this->_predefinedQueries[$name][2] : '')
            );
    }
    
    public function __call($name, $arguments)
    {
        return $this->executePredefinedQuery($name, $arguments);
    }
}
?>
