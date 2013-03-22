<?php
include 'file.class.php';
require 'interfaces/classMap.interface.php';
require 'interfaces/singleton.interface.php';

/** klasa do obsługi map klas
 * @author kadet {@link mailto:kadet@pog.ugu.pl napisz do mnie!}
 * @copyright
 * @access public
 * @license {@link http://creativecommons.org/licenses/by-nc-sa/3.0/pl/ CC 3.0}
 * @link nextra.xaa.pl
 * @name classMap
 * @version 1.1
 * @package bootLoad
 **/
class classMap
{
    /** przechowuje mapę plików
     * @access public
     * @var array
     */
    public $map = array();

    private $_pattern = '$2';

    /** pobiera mapę plików i zapisuje ją do zmiennej mapy
     * @access public
     * @param string $sDirectory
     * @return arrray
     */
    public function getMap()
    {
        $directories = func_get_args();

        foreach($directories as $directory)
        {
            $map = array();

            $pattern = $this->_pattern;

            if(is_array($directory))
            {
                $pattern = $directory[1];
                $directory = $directory[0];
            }

            $directory = substr($directory, -1) == '/' ?
                substr($directory, 0, strlen($directory) - 1) :
                $directory;

            # regex that checks file path
            $regex = str_replace(array('/', '.', '*'), array('\/', '\.', '(.*?)'), $directory).'\/(.*?\/)*'.'(.*?)\.(plugin|interface|class)\.php';
            $regex = '/^'.$regex.'$/';

            # get base directory
            $directory = strstr($directory, '*') !== false ? strstr($directory, '*', true) : $directory;

            # skip if dir not exists
            if(!file_exists($directory)) continue;

            # create iterator
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::UNIX_PATHS), RecursiveIteratorIterator::SELF_FIRST);
            foreach($iterator as $file)
            {
                $path = $file->getPathname();

                # skip invalid dirs
                if(!preg_match($regex, $file->getPathname())) continue;
                if(strpos($file->getPath(), '.svn') !== false) continue;

                # add to map
                if(strpos($file->getFilename(), '.plugin.php') !== false)
                    $map[preg_replace($regex, $pattern, $file->getPathname())."Plugin"] = $file->getPathname();
                if(strpos($file->getFilename(), '.interface.php') !== false)
                    $map[preg_replace($regex, $pattern, $file->getPathname())."Interface"] = $file->getPathname();
                if(strpos($file->getFilename(), '.class.php') !== false)
                    $map[preg_replace($regex, $pattern, $file->getPathname())] = $file->getPathname();
            }

            $this->map = array_merge($this->map, $map);
        }
        asort($this->map);
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
        $this->map = array_merge($this->map, unserialize($file->read()));
        return $this->map;
    }
}
?>
