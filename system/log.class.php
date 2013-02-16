<?php

/** klasa do obsługi logów
  * @author kadet {@link mailto:kadet@pog.ugu.pl napisz do mnie!}
  * @copyright by kadet
  * @access public
  * @license  http://creativecommons.org/licenses/by-nc-sa/3.0/pl/{@link}
  * @link nextra.xaa.pl
  * @name file
  * @version 1.1
  * @package Exception
  **/
class log
{
    #zmienne chronione

    /** nazwa pliku loga
     * @access protected
     * @var string
     */
    protected $_logFileName;

    /** obiekt pliku loga
     * @access protected
     * @var object
     */
    //protected $_logFile;

    /** konstruktor
     * @access public
     * @param string $logFileName nazwa pliku w którym ma zostać umieszczony log
     */
    public function __construct($logFileName)
    {
        if(!is_string($logFileName)) throw new frameworkException('Param $fileName must be a string!', 1365, nextraException::LVL_NORMAL);
        $this->_logFileName = $logFileName;
        //$this->_logFile = new file($logFileName, file::WRITE);
    }

    /**
     * @access public
     * @param string $tekst2Write tekst do zapisania do loga
     * @param string $prefix prefix tekstu loga [optional]
     */
    public function addToLog($tekst2Write, $prefix = '')
    {
        //$this->_logFile->write('['.date('H:i:s j.m.o').'] '.$prefix.': '.$tekst2Write."\n");
        file_put_contents($this->_logFileName, '['.date('H:i:s j.m.o').'] '.$prefix.': '.$tekst2Write."\n", FILE_APPEND);
    }
    
    public function clearLog()
    {
        file_put_contents($this->_logFileName, '');
    }
}
?>
