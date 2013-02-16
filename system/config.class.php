<?php
/** Config
  * @author @author kadet {@link mailto:kadet1090@gmail.com napisz do mnie!}
  * @copyright
  **/

/** klasa odpowiadająca za konfiguracje
 *
  * @author @author kadet {@link mailto:kadet1090@gmail.com napisz do mnie!}
  * @copyright
  * @access public
  * @example /exemples/config.exemple.php
  * @license {@link http://creativecommons.org/licenses/by-nc-sa/3.0/pl/}
  * @name config
  * @version 1.0
  * @package configuration
  **/

class config implements Countable, IteratorAggregate
{
    /** ścierzka do pliku
      * @access protected
      * @var object
      **/
    protected $_dataFile;

    /** Konstruktor
      * @access protected
      * @param string $file ścieżka do pliku
      * @param int $type typ pliku
      **/
    public function  __construct(dataFile $dataFile)
    {
        $this->_dataFile = $dataFile;
        $this->_dataFile->load();
    }

    /** Metoda magiczna GET
      * @access public
      * @param string $name
      * @return mixed
      **/
    public function  __get($name)
    {
        return ($this->_dataFile->$name ? $this->_dataFile->$name : null);
    }
    
    /** Metoda magiczna GET
      * @access public
      * @param string $name
      * @param mixed $value
      **/
    public function  __set($name, $value)
    {
        $this->_dataFile->$name = $value;
    }

    /** zwraca ilość elementów konfiguracji
      * @access public
      * @return int
      **/
    public function count()
    {
        return $this->_dataFile->count();
    }

    /** Zwraca wszystkie elementy konfiguracji
      * @access public
      * @param array $array [optional]
      **/
    protected function getHTML($array = null)
    {
        if(!isset($array))
            $array = ObjectToArray($this->_config);
        
        $list = '<ul>\n';
        foreach ($array as $key => $value)
        {
            if($key != '@attributes')
            {
                if(is_array($value))
                    $list .= '<li><b>'.$key.'</b> : '.$this->getHTML($value).'</li>';
                else
                    empty($value) ? $list .= '<li><b>'.$key.'</b></li>\n' : $list .= '<li><b>'.$key.'</b> : '.$value.'</li>\n';
            }
        }
        $list .= '</ul>\n';
        return $list;
    }

    /** metoda magiczna ISSET
      * @access public
      * @param string $name
      * @return bool
      **/
    public function  __isset($name)
    {
        return isset($this->_dataFile->$name);
    }

    /** Zamienia obiekt w ciąg znaków
      * @access public
      * @return string
      **/
    public function  __toString()
    {
        return $this->getHTML();
    }

    /** Ustala typ iterowania dla obiektu tej klasy
 
     * @access public
     * @return ArrayIterator
     */
    public function  getIterator()
    {
        $iterator = new RecursiveArrayIterator();
        return $iterator;
    }
    
    public function saveToFile()
    {
        $this->_dataFile->save();
    }
}

?>