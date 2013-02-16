<?php
include 'file.class.php';
require 'interfaces/classMap.interface.php';
require 'interfaces/singleton.interface.php';
/** @author kadet {@link mailto:kadet1090@gmail.com napisz do mnie!}
  * @license  http://creativecommons.org/licenses/by-nc-sa/3.0/pl/{@link}
  * @name classMap
  * @package bootLoad
  **/

/** klasa do obsługi map klas
  * @author kadet {@link mailto:kadet@pog.ugu.pl napisz do mnie!}
  * @copyright
  * @access public
  * @license {@link http://creativecommons.org/licenses/by-nc-sa/3.0/pl/ CC 3.0}
  * @link nextra.xaa.pl
  * @name classMap
  * @version 1.0
  * @package bootLoad
  **/
class classMap implements singleton, classMapInterface
{

    /** przechowuje mapę plików
     * @access public
     * @var array
     */
    public $map;

    /** przechowuje instancję obiektu
     * @access protected
     * @staticvar classMap
     */
    protected static $instance;

    /** pobiera instancję obiektu
     * @static
     * @access public
     * @return classMap
     */
    public static function  getInstance()
    {
        if(self::$instance == null)
            self::$instance = new classMap();
        return self::$instance;
    }

    /** blokada konstruktora
     * @access protected
     */
    protected function  __construct()   {   }
    /** pobiera mapę plików i zapisuje ją do zmiennej mapy
     * @access public
     * @param string $directory
     * @return arrray
     */
    public function getMap()
    {
        if(!isset($this->map))
        {
            $directories = func_get_args();
            foreach($directories as $directory)
            {
                $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory), RecursiveIteratorIterator::SELF_FIRST);
                foreach($it as $file)
                {
                    if($file->isDir() || strstr($file->getPath(), ".svn"))
                        continue;
                    else
                    {
                        if(strstr($file->getFilename(), '.class.php') != false)
                            $map[strStrBefore($file->getFilename(), '.class.php')] = $file->getPath().DIRECTORY_SEPARATOR.$file->getFilename();
                        elseif(strstr($file->getFilename(), '.interface.php') != false)
                            $map[strStrBefore($file->getFilename(), '.interface.php').'Interface'] = $file->getPath().DIRECTORY_SEPARATOR.$file->getFilename();
                        else
                            continue;
                    }
                }
            }
            var_dump($map);
            $this->map = $map;
            return $map;
        }
        else
            return $this->map;
    }

    /** Zapisuje mapę do wskazanego pliku
     * @access public
     * @param string $fileName
     * @param array $map
     */
    public function saveMapToFile($fileName)
    {
        if(empty($this->map)) $this->getMap();
        $map = $this->map;
        $map = serialize($map);
        $file = new file((string)$fileName, file::WRITE);
        $file->write($map);
    }

    /** wczytuje mapę z pliku
     * @access public
     * @param string $fileName
     */
    public function loadMapFromFile($fileName)
    {
        $file = new file((string)$fileName, file::READ);
        $this->map = unserialize($file->read());
    }
}
?>
