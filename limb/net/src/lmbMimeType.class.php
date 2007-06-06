<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbMimeType.
 *
 * @package net
 * @version $Id: lmbMimeType.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class lmbMimeType
{
  static protected $mime_types = array(
    'doc' => 'application/msword',
    'xls' => 'application/vnd.ms-excel',
    'ppt' => 'application/vnd.ms-powerpoint',
    'pdf' => 'application/pdf',
    'zip' => 'application/zip',
    'swf' => 'application/x-shockwave-flash',
    'zip' => 'application/x-zip-compressed',
    'wav' => 'audio/x-wav',
    'mpeg' => 'audio/mpeg',
    'bmp' => 'image/bmp',
    'gif' => 'image/gif',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/pjpeg',
    'png' => 'image/png',
    'psd' => 'image/psd',
    'msg' => 'message/rfc822',
    'html' => 'text/html',
    'txt' => 'text/plain',
    'rtf' => 'text/rtf',
    'avi' => 'video/avi',
    'mpg' => 'video/mpeg',
    'js' => 'text/javascript',
    'flv' => 'video/x-flv'
  );

  static protected $flipped_mime_types = array();

  static function getExtension($mime_type)
  {
    if(!self :: $flipped_mime_types)
      self :: $flipped_mime_types = array_flip(self :: $mime_types);

    $mime_type = strtolower($mime_type);

    return isset(self :: $flipped_mime_types[$mime_type])
      ? self :: $flipped_mime_types[$mime_type]
      : null;
  }

  static function getMimeType($extension)
  {
    $extension = ltrim(strtolower($extension), '.');

    return isset(self :: $mime_types[$extension])
      ? self :: $mime_types[$extension]
      : null;
  }

  static function getFileMimeType($file)
  {
    if($info = pathinfo($file))
    {
      if(isset($info['extension']))
        return self :: getMimeType($info['extension']);
    }
  }
}
?>