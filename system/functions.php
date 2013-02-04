<?php
if(PHP_VERSION_ID < 50300)
{
    /** replecament for strstr(...,...,1) in php 5.2
      * @access public
      * @param string $haystack
      * @param mixed $mNeedle
      * @return array | bool
      **/
    function strStrBefore($haystack,$mNeedle)
    {
	return array_shift(explode($mNeedle,$haystack,2));
    }
}
else
{
    /** Compatibility with >= 5.3
      * @access public
      * @param string $haystack
      * @param mixed $mNeedle
      * @return array | bool
      **/
    function strStrBefore($haystack,$mNeedle)
    {
	return strstr($haystack, $mNeedle, true);
    }
}

/** Zmienia tablicę na obiekt podanego typu
  * @access protected
  * @param array $array
  * @param string $oname
  **/
function ArrayToObject($array, $oname = 'stdClass')
{
    if(!is_array($array))
	return $array;
    
    $object = new $oname();
    if (is_array($array) && !empty($array))
    {
	foreach ($array as $name => $value)
	{
	    $name = strtolower(trim($name));
	    if (!empty($name) || $name === '0' || $name != '@attributes')
		$object->$name = ArrayToObject($value, $oname);
	}
	return $object;
    } 
    else
	return $array;
}


/** Zmienia obiekt na tablice
  * @access protected
  * @param object $object
  * @return array
  **/
function ObjectToArray($object)
{
    if(!is_object($object))
	return $object;

    $array = array();
    
    if(is_object($object) && !empty($object))
    {
	foreach((array)$object as $name => $value)
	{
	    $name = strtolower(trim($name));
	    if(!empty($name) || $name === '0')
	    {
		$array[$name] = ObjectToArray($value);
	    }
	}
	return $array;
    }
    else
	return $object;
}

function nl($text, $br = false)
{
    $text = trim($text);
    if($br)
	$text = preg_replace('<br />', '%\n%', $text);
    else
    {
        $tags = array('ul', 'li', 'table', 'tr', 'td', 'th', 'ol', 'pre');
	$text = str_replace("\n", '<br />', $text);
        $text = preg_replace('#<br />(</?('.implode('|', $tags).'))#', '$1', $text);
	
	if(preg_match_all('/\<pre (.*?)\>(.*?)\<\/pre\>/', $text, $match))
	    for($i = 0, $count = count($match[0]); $i < $count; $i++)
		$text = str_replace('<pre '.$match[1][$i].'>'.$match[2][$i].'</pre>','<pre '.$match[1][$i].'>'.str_replace('<br />', "\n", $match[2][$i]).'</pre>', $text);
    }
    
    return $text;
}
function array_diff2($array1, $array2) // aghh PHP SUX!
{
    foreach($array1 as $mKey => $mValue)
	if(array_search($mValue, $array2)) unset($array1[array_search($mValue, $array2)]);
    return $array1;
}

/** substr for HTML'ed string
 * @author modifed by kadet
 * @param string $text
 * @param int $lenght
 * @return string 
 */
function substrws($text, $lenght)
{
    $text = trim($text);
    $lenght += mb_strlen($text) - mb_strlen(strip_tags($text));
    $breakPos = mb_strpos($text, '<hr class="break" />');
    
    if($breakPos > 0)
    {
	return mb_substr($text, 0, $breakPos);
    }
    elseif((mb_strlen($text) > $lenght)) 
    {
        $whitespaceposition = mb_strpos($text, ' ', $lenght) - 1;
        if($whitespaceposition > 0) 
	{

            $chars = count_chars(mb_substr($text, 0, ($whitespaceposition + 1)), 1);
            if((isset($chars[ord('<')]) ? $chars[ord('<')] : 0) > (isset($chars[ord('>')]) ? $chars[ord('>')] : 0)) 
	    {
                $whitespaceposition = mb_strpos($text, ">", $whitespaceposition);
            }
            $text = mb_substr($text, 0, ($whitespaceposition + 1));
        }
        // close unclosed html tags
        if(preg_match_all("|(<([\w]+)[^>]*>)|", $text, $buffer)) 
	{	
            if(!empty($buffer[1])) 
	    {
                preg_match_all("|</([a-zA-Z]+)>|", $text, $buffer2);
                if(count($buffer[2]) != count($buffer2[1])) 
		{
		    $array1 = ($buffer[2]);
		    $array2 = ($buffer2[1]);
                    $closing_tags = array_diff2($array1, $array2);
                    $closing_tags = array_reverse($closing_tags);
                    foreach($closing_tags as $tag) 
		    {
			if($tag != "br" && $tag != "img")
			    $text .= '</'.$tag.'>';
                    }
                }
            }
        }
    }
    return $text;
}

function prepareMenu($menu, $parent = NULL, $self = NULL)
{
    $return = array();
    
    foreach($menu as $element) 
	if($element->parent == $parent)
	    $return[(int)$element->pos] = prepareMenu($menu, $element->id, $element);
        
    if($self != NULL) $return['self'] = $self;
    
    ksort($return);
    return is_array($return) ? $return : array($return);
}

function pass($password)
{
    return md5(base64_encode($password."4#Q@T?"));
}

function isMail($mail)
{
    return preg_match('/^[a-zA-Z0-9\_\-\.]{1,256}@[a-z\_\-\.]*\.[a-z]{2,4}/', $mail);
}

/** deletes ele
 * @access public
 * @param array $array
 * @param int $number
 * @return array
 */
function arrayDelete(array $array, $number)
{
    for($i = 0; $i < $number; $i++)
        array_shift($array);
    return $array;
}

function array_grep(array $array, $regexp)
{
    $result = array();
    foreach($array as $key => $val) 
	if(preg_match($regexp, $key))
	    $result[$key] = $val;
    return $result;
}

// TODO: clean
function getPermissions($permissions, $current) {
    $permissions = explode(', ', str_replace('*', 'all', $permissions));
    
    foreach($permissions as $perm) {
        $active = !(substr($perm, 0, 1) == '-');
        
        $perm = substr($perm, 0, 1) != '-' ?
                $perm :
                substr($perm, 1);
        
        if(strpos($perm, '/') === false)
            $perm .= '/all';
        
        # get category and name of permission
        $category = strstr($perm, '/', true);
        $name     = substr(strstr($perm, '/'), 1);
        
        $current[$category][$name] = $active;
    }
    
    return $current;
}
?>