<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BBcode
 *
 * @author admin
 */
class BBcode 
{
    protected static $_bBcodeExp = array(
        'html' => array(),
        'callback' => array()
    );
    
    protected static $_exp = array(
        'html' => array(),
        'callback' => array()
    );
    
    public static function loadBBcode(xml $BBcode)
    {
	$BBcode->load();
	foreach($BBcode->BBcode as $code)
	{
	    $params = array();
	    $code['bbCode'] = '\['.$code['tag'];

	    if($code['param'] == 'true') 
	    {
		$params[0] = 'param';
		$code['bbCode'] .= '=&quot;(.*?)&quot;';
	    }
            
	    if(isset($code['params']))
	    {
		$params = explode(', ', $code['params']);
		foreach($params as $param)
		    $code['bbCode'] .= ' '.$param.'=&quot;(.*?)&quot;';
	    }
            
	    $code['bbCode'] .= '\]';
	    if($code['closeTag'] == 'true') $code['bbCode'] .= '(.*?)\[/'.$code['tag'].'\]';
            if(isset($code['callback']))
            {
                $code['callback'] = str_replace('{text}', '$matches['.(count($params)+1).']', $code['callback']);
                
                foreach($params as $key => $name)
                    $code['callback'] = str_replace('{'.$name.'}', '$matches['.($key+1).']', $code['callback']);
                
                self::$_bBcodeExp['callback'][] = '#'.$code['bbCode'].'#si';
                self::$_exp['callback'][] = create_function('$matches', $code['callback']);
            }
            else
            {
                $code['html'] = str_replace('{text}', '$'.(count($params)+1), $code['html']);

                foreach($params as $key => $name)
                    $code['html'] = str_replace('{'.$name.'}', '$'.($key+1), $code['html']);

                self::$_bBcodeExp['html'][] = '#'.$code['bbCode'].'#si';
                self::$_exp['html'][] = $code['html'];
            }
	}
    }
    
    public static function parse($bcodeString)
    {
        $bcodeString = preg_replace_callback('#\[code(.*?)\](.*?)\[/code\]#si', create_function('$matches', 'return "[code".$matches[1]."]".str_replace("]", "\\]", $matches[2])."[/code]";'), $bcodeString);
	$bcodeString = str_replace('\\]', ']', preg_replace(self::$_bBcodeExp["html"], self::$_exp["html"], str_replace('"', '&quot;', $bcodeString)));
    
        foreach(self::$_bBcodeExp['callback'] as $no => $exp) {
            $bcodeString = preg_replace_callback($exp, self::$_exp["callback"][$no], $bcodeString);
        }
        
        return $bcodeString;
    }
}
?>
