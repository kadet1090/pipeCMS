<?php
require 'interfaces/autoloader.interface.php';
/** odpowiada za automatyczne wczytanie pliku z klasą
  * @author kadet {@link mailto:kadet1090@gmail.com napisz do mnie!}
  * @copyright
  * @access public
  * @license  http://creativecommons.org/licenses/by-nc-sa/3.0/pl/{@link}
  * @name autoLoader
  * @version 1.0
  * @package bootLoad
  **/
class autoloader implements autoloaderInterface
{
    #zmienne chronione

    /** Prefix
      * @access protected
      * @var string
      **/
    protected $_prefix;

    /** Postfix
      * @access protected
      * @var string
      **/
    protected $_postfix;
    /** Nazwa katalogu do przeszukania
      * @access protected
      * @var string
      **/
    protected $_directory;

    /**
     * @access protected
     * @var ClassMap
     */
    public $_classMap;

    #zmienne publiczne
    /** Prefix - tak/nie
      * @access protected
      * @var string
      **/
    public $usePrefix = false;

    /** postfix - tak/nie
      * @access protected
      * @var string
      **/
    public $usePostfix = false;
    
    /** używanie mapy - tak/nie
      * @access protected
      * @var string
      **/
    public $useClassMap = true;

    /** zarejestrowany - tak/nie
      * @access protected
      * @var string
      **/
    private $ragistred = false;

    /** konstruktor
     * @access public
     * @param string $directory katalog
     * @param string $prefix proefix
     * @param string $postFix postfix
     * @param array $config dodatkowe ustawienia
     */
    public function  __construct($directory, $config = array(), classMap $classMap = null, $prefix = '', $postFix = '')
    {
        #walidacja Katalogu
        if(is_string($directory))
        {
            if(!is_dir($directory))
               throw new Exception($directory.' is not a directory', 100);
        }
        else
            throw new Exception('Directory name must be a string!', 101);

        #ustalanie prefixu i postfixu
        $this->_prefix = (string)$prefix;
        $this->_postfix = (string)$postFix;

        #sprawdzanie dodatkowych ustawień
        if(array_key_exists('usePrefix', $config)) is_bool($config['usePrefix']) ? $this->prefix = $config['usePrefix'] : false;
        if(array_key_exists('usePostfix', $config)) is_bool($config['usePostfix']) ? $this->usePostfix = $config['usePostfix'] : false;
        if(array_key_exists('useClassMap', $config)) is_bool($config['useClassMap']) ? $this->useClassMap = $config['useClassMap'] : true;
        if(($this->useClassMap === true || $config['useClassMap']) && isset($classMap)) $this->_classMap = $classMap;
    }

    /** wczytywanie klasy
     * @access public
     * @param string $className
     */
    public function load($className)
    {
        $classPath = $this->usePrefix ? $this->_prefix : null.'.';
        $classPath .= $className;
        $classPath .= '.'.$this->usePostfix ? $this->_postfix : null;

        if($this->useClassMap == true)
        {
            $this->_classMap->map ? null : $this->_classMap->getMap();
            foreach($this->_classMap->map as $nameOfClass => $path)
            {                
                if($className == $nameOfClass)
                {
                    require $path;
                }
            }
        }
        else
        {
            if(file_exists($classPath.'.class.php') && file_exists($classPath.'.interface.php'))
            {
                require $classPath.'.class.php';
            }
            else
            {
                new Exception('File '.$classPath.'.class.php is not exisit!', 102);
            }
        }

    }

    /** rejestracja funkcji load
     * @access public
     */
    public function ragister()
    {
        if(!$this->ragistred)
        {
            spl_autoload_register(array($this, 'load'));
            $this->ragistred = true;
        }
    }

}
?>
