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
    protected static $_BBcodeExp = array(
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
            if(isset($code['callback']))
            {
                self::addCallback(
                    $code["tag"], 
                    $code['callback'], 
                    $code['closeTag'] == 'true', 
                    $code['param'] == 'true' ? true : (
                        isset($code['params']) ? explode(', ', $code['params']) : false
                    )
                );
            }
            else
            {
                self::addHtml(
                    $code["tag"], 
                    $code['html'], 
                    $code['closeTag'] == 'true', 
                    $code['param'] == 'true' ? true : (
                        isset($code['params']) ? explode(', ', $code['params']) : false
                    )
                );
            }
        }
  //      var_dump(self::$_exp, self::$_BBcodeExp);
    }
    
    public static function addHtml($tag, $html, $close = false, $params = false) {
        $regex = self::_regex($tag, $close, $params);
       
        if($params === true)
            $params = array('param');
        elseif(!is_array($params))
            $params = array();
        
        $html = str_replace('{text}', '$'.(count($params)+1), $html);
        foreach($params as $key => $name)
            $html = str_replace('{'.$name.'}', '$'.($key+1), $html);

        self::$_BBcodeExp['html'][] = '#'.$regex.'#si';
        self::$_exp['html'][] = $html;
    }
    
    public static function addCallback($tag, $callback, $close = false, $params = false) {
        $regex = self::_regex($tag, $close, $params);
       
        if($params === true)
            $params = array('param');
        elseif(!is_array($params))
            $params = array();
        
        foreach($params as $key => $name)
            $callback = str_replace('{'.$name.'}', '$matches['.($key+1).']', $callback);

        self::$_BBcodeExp['callback'][] = '#'.$regex.'#si';
        self::$_exp['callback'][] = create_function('$matches', $callback);
    }
    
    private static function _regex($tag, $close, $params) {
        $regex  = '\['.$tag;

        if($params === true) 
            $regex .= '=&quot;(.*?)&quot;';
        elseif(is_array($params))
            foreach($params as $param)
                $regex .= ' '.$param.'=&quot;(.*?)&quot;';
        
        $regex .= '\]';
        
        if($close)
            $regex .= '(.*?)\[/'.$tag.'\]';
        
        return $regex;
    }
    
    public static function parse($BBcodeString)
    {        
        $md5 = md5($BBcodeString);
        
        if(cache::available('BBcode', $md5))
            return cache::get('BBcode', $md5);
        
        $BBcodeString = preg_replace_callback('#\[code(.*?)\](.*?)\[/code\]#si', create_function('$matches', 'return "[code".$matches[1]."]".str_replace("]", "\\]", $matches[2])."[/code]";'), $BBcodeString);
        $BBcodeString = preg_replace_callback('#\[bbcode\](.*?)\[/bbcode\]#si', create_function('$matches', 'return "[bbcode]".str_replace("]", "\\]", $matches[1])."[/bbcode]";'), $BBcodeString);
        $BBcodeString = str_replace('"', '&quot;', $BBcodeString);
        
        // Don't ask.... :D
        $pattern = '#('.str_replace(array('#si', '#'), '', implode('|', self::$_BBcodeExp['html'])).')#si';
        while(preg_match($pattern, $BBcodeString))
             $BBcodeString = preg_replace(self::$_BBcodeExp["html"], self::$_exp["html"], $BBcodeString);
        
        foreach(self::$_BBcodeExp['callback'] as $no => $exp) 
            $BBcodeString = preg_replace_callback($exp, self::$_exp["callback"][$no], $BBcodeString);
        
        $parsed = str_replace('\\]', ']', $BBcodeString);
        cache::set('BBcode', $md5, $parsed);
        
        return $parsed;
    }
}
?>
