<?php
/** @author kadet {@link mailto:kadet1090@gmail.com napisz do mnie!}
  * @license  http://creativecommons.org/licenses/by-nc-sa/3.0/pl/{@link}
  * @name router
  * @package bootLoad
  **/

/** Prepares URI for CMS
  * @author kadet {@link mailto:kadet@gmail.com write to me!}
  * @copyright
  * @access public
  * @license  http://creativecommons.org/licenses/by-nc-sa/3.0/pl/{@link}
  * @link pipe-software.xaa.pl
  * @name router
  * @version 1.1.1
  * @package bootLoad
  **/
class router implements singleton, routerInterface
{
    /** Static url chunks
     * @access protected
     * @var    Array
     */
    protected $_staticParams = array();
    
    /** regeXP url chunks
     * @access protected
     * @var    Array
     */
    protected $_regexParams = array();
    protected $_params = array();
    protected $_delimeter = '/';
    protected $_space = '-';
    protected $_extension = '';
    
    /** Default URI
     * @access protected
     * @var    string
     */
    protected $_uRI = array('index', 'index');

    /** Singleton instance
     * @access protected
     * @var router
     */
    protected static $_instance = null;

    /** Blocks new foo($bar);
     * @access protected
     */
    protected function __construct() {  }

    /** Singleton
     * @static
     * @access public
     * @return router
     */
    public static function getInstance()
    {
        if(!isset(self::$_instance))
        {
            self::$_instance = new router();
        }
        return self::$_instance;
    }

    /** Adds one static url chunk
     * @static
     * @access public
     * @param string $routeName
     */
    public static function addStaticParam($paramName, $mParamDefaultValue)
    {
        $this->_staticParams[$paramName] = $mParamDefaultValue;
    }

    /** Decodes URI
     * @access public
     * @param string $uRL
     * @return array
     */
    public function decodeURI()
    {
        #variables definition
        $returnURL = array();
        $returnPath = array();
	
        #decodes static paremeters
        if(is_array($this->_uRI)) $uRL = $this->_uRI;
	else $uRL = explode($this->_delimeter, $this->_uRI);
	//print_r($this->_uRI); // DEBUG!
	$blockPosition = 0;
        foreach($this->_staticParams as $paramName => $defaultParamValue) // assign chunks to names
        {
            $returnURL[$paramName] = (isset($uRL[$blockPosition]) ? $uRL[$blockPosition] : $defaultParamValue);
	    $blockPosition++;
        }
        $uRL = arrayDelete($uRL, count($this->_staticParams)); // cleaning :)

        #decoding regexp params
        $uRI = implode($this->_delimeter, $uRL);
        foreach($this->_regexParams as $params) // preparing params for regexp testing
            $param[implode($this->_delimeter, array_keys($params))] = implode($this->_delimeter, array_values($params));

	$found = false;
        foreach($param as $name => $regeXP) // going through regeXP array, for science.
        {
	    $returnPath = array_flip(explode($this->_delimeter, $name));
	    //echo $uRI.' : '.$regeXP.' : '.preg_match('/^'.$regeXP.'$/i', $uRI); // DEBUG!
            if(preg_match('/^'.$regeXP.'$/i', $uRI)) // testing...
            {
		$found = true;
                $i = 0;
                $uRLBlocks = explode($this->_delimeter, $uRI);
                foreach($returnPath as $name => $mValue) // przypisuje wartości parametrów do ich nazw
                {
                    $returnURL[$name] = $uRLBlocks[$i];
                    $i++;
                }
            }
        }
        
	if(!$found) // gdy nie ma żadnej zgodności przypisuje indeksy od 0
	    $returnPath = explode($this->_delimeter, $uRI);
        $this->_params = $returnURL; // łączenie statycznych i dynamicznych parametrów
    }

    /** Dodaje paramtery
     * @access public
     * @param array $paramNames
     * @param array $paramRegeXP
     */
    public function addParams(array $paramNames, array $paramRegeXP)
    {
        if(count($paramNames, COUNT_NORMAL) == count($paramRegeXP, COUNT_NORMAL))
        {
            $routesCount = count($this->_regexParams);
            $paramCount = count($paramNames, COUNT_NORMAL);
            for($i = 0; $i < $paramCount; $i++)
            {
                $this->_regexParams[$routesCount+1][$paramNames[$i]] = $paramRegeXP[$i];
            }
        }
    }
    
    public function match($name)
    {
	if(isset($this->_regexParams[$name]))
	{
	    if(is_array($this->_uRI)) $uRL = $this->_uRI;
	    else $uRL = explode($this->_delimeter, $this->_uRI);
	    $uRL = arrayDelete($uRL, count($this->_staticParams));
	    
	    $param = implode($this->_delimeter, array_values($this->_regexParams[$name]));
	    return (bool)preg_match('/^'.$param.'$/', implode($this->_delimeter, $uRL));
	}
	return false;
    }

    public function get($name = NULL)
    {
        return (isset($_GET[$name]) ? trim(addslashes($_GET[$name])) : null);
    }

    public function post($name = NULL)
    {
	if($name == NULL)
	    return $_POST;
	else
	{
	    $s = (isset($_POST[$name]) ? $_POST[$name] : null);
	    return (is_string($s) ? trim($s) : $s);
	}
    }

    public function loadFromConfig(config $config)
    {
	if(isset($config->delimeter))	$this->_delimeter  = $config->delimeter;
	if(isset($config->extension))	$this->_extension  = $config->extension;
	if(isset($config->space))	$this->_space	    = $config->space;
	
	foreach($config->staticParams->staticParam as $param)
	{
	    $paramName = $param['name'];
	    $mParamValue = $param['default'];
	    $this->_staticParams[(string)$paramName] = (string)$mParamValue;
	}
	
        foreach($config->regEXPparams->paramGroup as $paramGroup)
	{
	    foreach($paramGroup->param as $param)
	    {
		$this->_regexParams[(string)$paramGroup['name']][(string)$param['name']] = $param['regEXP'];
	    }
	}
	
    }
    
    public function setURI($uRI)
    {
	$this->_uRI = $uRI;
    }
    
    public function __get($name)
    {
	return (isset($this->_params[$name]) ? $this->_params[$name] : null);
    }
    
    public function prepareLink()
    {
	$funcArgs = func_get_args();
	
	if(is_array($funcArgs[0])) $args = $funcArgs[0];
	else $args = $funcArgs;
	if(func_num_args() > 1 && is_array($funcArgs[0]))
	{
	    foreach($funcArgs as $key => $value)
	    {
		if($key == 0) continue;
		$args[] = $value;
	    }
	}
	if(preg_match('#http://(.*?)#', $args[0])) return $args[0];
	//BIG THANKS to thek from php.pl for this array :D
	$utf8 = array( 'à' => 'a',  'ô' => 'o',  'ď' => 'd',  'ḟ' => 'f',  'ë' => 'e',  'š' => 's',
	    'ơ' => 'o', 'ß' => 'ss', 'ă' => 'a',  'ř' => 'r',  'ț' => 't',  'ň' => 'n',  'ā' => 'a',
	    'ķ' => 'k', 'ŝ' => 's',  'ỳ' => 'y',  'ņ' => 'n',  'ĺ' => 'l',  'ħ' => 'h',  'ṗ' => 'p',
	    'ó' => 'o', 'ú' => 'u',  'ě' => 'e',  'é' => 'e',  'ç' => 'c',  'ẁ' => 'w',  'ċ' => 'c',
	    'õ' => 'o', 'ṡ' => 's',  'ø' => 'o',  'ģ' => 'g',  'ŧ' => 't',  'ș' => 's',  'ė' => 'e',
	    'ĉ' => 'c', 'ś' => 's',  'î' => 'i',  'ű' => 'u',  'ć' => 'c',  'ę' => 'e',  'ŵ' => 'w',
	    'ṫ' => 't', 'ū' => 'u',  'č' => 'c',  'ö' => 'o',  'è' => 'e',  'ŷ' => 'y',  'ą' => 'a',
	    'ł' => 'l', 'ų' => 'u',  'ů' => 'u',  'ş' => 's',  'ğ' => 'g',  'ļ' => 'l',  'ƒ' => 'f',
	    'ž' => 'z', 'ẃ' => 'w',  'ḃ' => 'b',  'å' => 'a',  'ì' => 'i',  'ï' => 'i',  'ḋ' => 'd',
	    'ť' => 't', 'ŗ' => 'r',  'ä' => 'a',  'í' => 'i',  'ŕ' => 'r',  'ê' => 'e',  'ü' => 'u',
	    'ò' => 'o',  'ē' => 'e',  'ñ' => 'n',  'ń' => 'n',  'ĥ' => 'h',  'ĝ' => 'g',  'đ' => 'd',
	    'ĵ' => 'j', 'ÿ' => 'y',  'ũ' => 'u',  'ŭ' => 'u',  'ư' => 'u',  'ţ' => 't',  'ý' => 'y',
	    'ő' => 'o', 'â' => 'a',  'ľ' => 'l',  'ẅ' => 'w',  'ż' => 'z',  'ī' => 'i',  'ã' => 'a',
	    'ġ' => 'g', 'ṁ' => 'm',  'ō' => 'o',  'ĩ' => 'i',  'ù' => 'u',  'į' => 'i',  'ź' => 'z',
	    'á' => 'a', 'û' => 'u',  'þ' => 'th', 'ð' => 'dh', 'æ' => 'ae', 'µ' => 'u',  'ĕ' => 'e',
	    'À' => 'A',  'Ô' => 'O',  'Ď' => 'D',  'Ḟ' => 'F',  'Ë' => 'E',  'Š' => 'S',  'Ơ' => 'O',
	    'Ă' => 'A',  'Ř' => 'R',  'Ț' => 'T',  'Ň' => 'N',  'Ā' => 'A',  'Ķ' => 'K',  'Ĕ' => 'E',
	    'Ŝ' => 'S',  'Ỳ' => 'Y',  'Ņ' => 'N',  'Ĺ' => 'L',  'Ħ' => 'H',  'Ṗ' => 'P',  'Ó' => 'O',
	    'Ú' => 'U',  'Ě' => 'E',  'É' => 'E',  'Ç' => 'C',  'Ẁ' => 'W',  'Ċ' => 'C',  'Õ' => 'O',
	    'Ṡ' => 'S',  'Ø' => 'O',  'Ģ' => 'G',  'Ŧ' => 'T',  'Ș' => 'S',  'Ė' => 'E',  'Ĉ' => 'C',
	    'Ś' => 'S',  'Î' => 'I',  'Ű' => 'U',  'Ć' => 'C',  'Ę' => 'E',  'Ŵ' => 'W',  'Ṫ' => 'T',
	    'Ū' => 'U',  'Č' => 'C',  'Ö' => 'O',  'È' => 'E',  'Ŷ' => 'Y',  'Ą' => 'A',  'Ł' => 'L',
	    'Ų' => 'U',  'Ů' => 'U',  'Ş' => 'S',  'Ğ' => 'G',  'Ļ' => 'L',  'Ƒ' => 'F',  'Ž' => 'Z',
	    'Ẃ' => 'W',  'Ḃ' => 'B',  'Å' => 'A',  'Ì' => 'I',  'Ï' => 'I',  'Ḋ' => 'D',  'Ť' => 'T',
	    'Ŗ' => 'R',  'Ä' => 'A',  'Í' => 'I',  'Ŕ' => 'R',  'Ê' => 'E',  'Ü' => 'U',  'Ò' => 'O',
	    'Ē' => 'E',  'Ñ' => 'N',  'Ń' => 'N',  'Ĥ' => 'H',  'Ĝ' => 'G',  'Đ' => 'D',  'Ĵ' => 'J',
	    'Ÿ' => 'Y',  'Ũ' => 'U',  'Ŭ' => 'U',  'Ư' => 'U',  'Ţ' => 'T',  'Ý' => 'Y',  'Ő' => 'O',
	    'Â' => 'A',  'Ľ' => 'L',  'Ẅ' => 'W',  'Ż' => 'Z',  'Ī' => 'I',  'Ã' => 'A',  'Ġ' => 'G',
	    'Ṁ' => 'M',  'Ō' => 'O',  'Ĩ' => 'I',  'Ù' => 'U',  'Į' => 'I',  'Ź' => 'Z',  'Á' => 'A',
	    'Û' => 'U',  'Þ' => 'Th', 'Ð' => 'Dh', 'Æ' => 'Ae' );
        
	foreach($args as $d => $mArgument)
	{
	    if(is_string($mArgument)) 
	    {
		$mArgument = str_replace(array_keys($utf8), array_values($utf8), $mArgument);
		$mArgument = preg_replace('#[^-_A-Za-z0-9\s]+#si', '', $mArgument);
		$mArgument = preg_replace('#\s{2,}#si', ' ', $mArgument);
		$mArgument = str_replace(' ', $this->_space, $mArgument);
		$args[$d] = substr($mArgument, 0, 25);
	    }
	}
	return trim(implode($this->_delimeter, $args));
    }
}


?>
