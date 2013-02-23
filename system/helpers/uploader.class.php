<?php
/**
 * Description of fileUploadHelper
 *
 * @author admin
 */
class uploader
{
    const UPLOAD_ERROR   = 1220;
    const WRONG_TYPE     = 1221;
    const TOO_BIG        = 1222;
    const ALREADY_EXISTS = 1223;
    
    public static $uploadDirectory = './data/';
    
    public static function upload($fieldname, $directory, $filename = null, $overwrite = false, $accepted = null, $maxSize = -1)
    {
        $directory = self::$uploadDirectory.$directory.'/';
        if(!file_exists($directory))
            mkdir($directory, 0777, true);
        
        if(!is_uploaded_file($_FILES[$fieldname]['tmp_name']))
            throw new frameworkException("File upload error.", 1220);
        
        if($accepted != null && array_search($_FILES[$fieldname]['type'], $accepted) === false)
            throw new frameworkException("File type is not accepted.", 1221);
        
        if($maxSize != -1 && $_FILES[$fieldname]['szie'] > $maxSize)
            throw new frameworkException("File is too big.", 1222);
        
        $dest = $directory.($filename == null ? $_FILES[$fieldname]['name'] : $filename.strstr($_FILES[$fieldname]['name'], '.'));
        if(!$overwrite && file_exists($dest))
            throw new frameworkException("Destination file already exists.", 1223);
        
        move_uploaded_file($_FILES[$fieldname]['tmp_name'], $dest);
        return array('path' => $dest, 'type' => $_FILES[$fieldname]['type'], 'size' => $_FILES[$fieldname]['size']);
    }
}

?>
