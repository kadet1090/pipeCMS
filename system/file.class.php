<?php
/** klasa do obsługi map klas
  * @author kadet {@link mailto:kadet1090@gmail.com napisz do mnie!}
  * @copyright
  * @access public
  * @license  http://creativecommons.org/licenses/by-nc-sa/3.0/pl/{@link}
  * @link nextra.xaa.pl
  * @name file
  * @version 1.0
  * @package HelpClass
  **/
class file //implements fileInterface
{
    #flagi

    /** Flaga odpowiadająca za odczyt
     * @name READ
     * @access public
     */
    const READ = 1;

    /** Flaga odpowiadająca za zapis
     * @name WRITE
     * @access public
     */
    const WRITE = 2;

    /** Flaga odpowiadająca za zapis i odczyt
     * @name READWRITE
     * @access public
     */
    const READWRITE = 3;

    #zmienne prywatne i chronione

    /** Nazwa pliku do odczytu
     * @access protected
     * @var string
     */
    protected $_fileName;

    /** Zasób pliku
     * @access protected
     * @var resource
     */
    protected $_rHandle;

    #zmienne publiczne

    /** zmienna flagi służącej do rozróżniania czy plik jest do odczytu, zapisu, czy do odczytu i zapisu jednocześnie
     * @access public
     * @var int
     */
    public $flag = 1;

    #metody publiczne

    /** Konstruktor
     * @access public
     * @param string $fileName nazwa pliku który ma zostać otworzony
     * @param int $flag Flaga pliku [optional]
     */
    public function __construct($fileName, $flag = 1)
    {
        if(!is_string($fileName)) throw new nextraException('File name must be a string!', 1240, nextraException::LVL_FATAL);
        $this->_fileName = $fileName;
        $this->flag = $flag;
        switch($flag)
        {
            case self::READ:
                $this->_rHandle = fopen($fileName, 'r');
                break;
            case self::WRITE:
                $this->_rHandle = fopen($fileName, 'w');
                break;
            case self::READWRITE:
                $this->_rHandle = fopen($fileName, 'rw');
                break;
        }
    }

    /** odczytuje dane z pliku
     * @access public
     * @param int $length Informuje skrypt ile danych z pliku ma zostać odczytane
     * @return mixed
     */
    public function read($numberOfChars = null)
    {
        if(isset($this->flag))
        {
            if($this->flag == 1 || $this->flag == 3)
            {
                if(empty($numberOfChars))
                {
                    return file_get_contents($this->_fileName);
                }
                else
                {
                    return fgets($this->_rHandle, $numberOfChars);
                }
            }
        }
    }

    /** zapisuje w pliku podany w zmiennej $textToWrite tekst
     * @access public
     * @param string $textToWrite tekst do zapisania w pliku
     */
    public function write($text)
    {if(isset($this->flag))
        {
            if($this->flag == 2 || $this->flag == 3)
            {
                fwrite($this->_rHandle, $text);
            }
        }
    }

    /** Destruktor
     * @access public
     */
    public function __destruct()
    {
        fclose($this->_rHandle);
    }

    /** Zmienia obiekt w ciąg znaków
     * @access public
     * @return string
     */
    public function  __toString()
    {
        return (string)$this->read();
    }

    /** ustala flagę pliku
     * @access public
     * @param int $flag flaga Pliku
     */
    public function setFlag($flag)
    {
        fclose($this->_rHandle);
        switch($flag)
        {
            case self::READ:
                $this->_rHandle = fopen($this->_fileName, 'r');
                break;
            case self::WRITE:
                $this->_rHandle = fopen($this->_fileName, 'w');
                break;
            case self::READWRITE:
                $this->_rHandle = fopen($this->_fileName, 'rw');
                break;
        }
    }
}
?>
