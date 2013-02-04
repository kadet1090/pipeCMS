<?php
class captha 
{
    private function generateText()
    {
	$numbers = explode(", ", language::get("numbers"));
	$textCaptcha = array();
	$textCaptcha["first"] = rand(0, 9);
	$textCaptcha["operator"] = (bool)rand(0, 1);
	$textCaptcha["second"] = rand(0, 9);
	
	$textCapthaResult = array();
	$textCapthaResult["int"] = $textCaptcha["first"];
	if($textCaptcha["operator"]) $textCapthaResult["int"] += $textCaptcha["second"];
	else $textCapthaResult["int"] -= $textCaptcha["second"];
	$textCapthaResult["str"] = (rand(0, 1) ? $numbers[$textCaptcha["first"]] : $textCaptcha["first"]).' '.($textCaptcha["operator"] ? language::get("plus") : language::get("minus")).' '.(rand(0, 1) ? $numbers[$textCaptcha["second"]] : $textCaptcha["second"]);
	
	return $textCapthaResult;
    }
    
    private function generateImage()
    {	
	$chars		= "abcdefghijklmnopqrstuvwxyz123456789";
	$str		= "";
	for($h = 0, $length = (strlen($chars) - 1); $h < 8; ++$h)
	    $str .= substr($chars, mt_rand(0, $length), 1);
	
	$cfg		= array();
	$cfg["width"]	= 250;
	$cfg["height"]	= 80;
	
	$image		= imagecreate($cfg["width"], $cfg["height"]);
	
	$cfg["size"]	= 28;
	$cfg["font"]	= "./data/Tr2n.ttf";
	$cfg["bg"]	= imagecolorallocatealpha($image, 221, 221, 221, 127);
	$cfg["color"]	= imagecolorallocate($image, 0, 0, 0);
	
	$cfg["box"]	= imagettfbbox($cfg["size"], 0, $cfg["font"], $str);
	$cfg["x"]	= ($cfg["width"] - ($cfg["box"][4] - $cfg["box"][0])) / 2;
	$cfg["y"]	= ($cfg["height"] - ($cfg["box"][5] - $cfg["box"][1])) / 2;
	
	imagettftext($image, $cfg["size"], 0, $cfg["x"], $cfg["y"], $cfg["color"], $cfg["font"], $str);
	imagepng($image, "./data/tmp/captha/".md5($str).".png");
	imagedestroy($image);
	
	return array("str" => $str, "src" => "./data/tmp/captha/".md5($str).".png");
    }
    
    public function generate()
    {
	$text = $this->generateText();
	$img  = $this->generateImage();
	
	$_SESSION["textCaptcha"] = md5($text["int"]);
	$_SESSION["imgCaptcha"]  = md5($img["str"]);
	$_SESSION["imgSrc"]  = $img["src"];
	
	return '<img src="'.$img["src"].'" alt="'.$text["str"].'" title="captcha" />';
    }
    
    public function check($str)
    {
	if(md5(strtolower($str)) == $_SESSION["textCaptcha"] || md5(strtolower($str)) == $_SESSION["imgCaptcha"])
	    $result = true;
	else
	    $result = false;
	unlink($_SESSION["imgSrc"]);
	$_SESSION["textCaptcha"] = "";
	$_SESSION["imgCaptcha"]  = "";
	return $result;
    }
}

?>
