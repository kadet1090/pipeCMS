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

    public static $tableBindings = array(
        '%p%users' => 'user'
    );
    
    public function processResult(PDOStatement $result, $object = false, $DAOname = '')
    {
        if(empty($DAOname))
            $DAOname = $this->_defaultDAOname;
        if(!class_exists($DAOname))
            $DAOname = 'stdDao';

        $res = array();
        if(preg_match('/^[^A-Z_]*SELECT[^A-Z_]/i', $result->queryString))
        {
            $columns = array();
            for($i = 0, $count = $result->columnCount(); $i < $count; $i++) {
                $columns[] = $result->getColumnMeta($i);
            }

            $result->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $DAOname, array($columns, get_called_class()));

            if($result->rowCount() > 1 || !$object)
            {
                while($row = $result->fetch())
                    $res[] = $row;
            }
            else
                $res = $result->fetch();

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

        $this->isValid($name, $arguments);

        foreach($arguments as $argID => $argument)
        {
            if(is_int($argument))
                $query = str_replace('['.($argID+1).']', $argument, $query);
            elseif(is_string($argument))
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

    public function getFoundCount() {
        return self::$connection->executeQuery('SELECT FOUND_ROWS()')->fetchColumn();
    }
}
?>
