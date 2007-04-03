<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMimeType.class.php 5001 2007-02-08 15:36:45Z pachanga $
 * @package    net
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
    'js' => 'text/javascript'
    );
  static protected $flipped_mime_types = NULL;

  static function getExtension($mime_type)
  {
    $mime_type = strtolower($mime_type);
    if ( !is_array(self :: $flipped_mime_types) )
    {
      self :: $flipped_mime_types = array_flip(self :: $mime_types);
    }

    return isset(self :: $flipped_mime_types[$mime_type])
      ? self :: $flipped_mime_types[$mime_type]
      : NULL;
  }

  static function getMimeType($extension)
  {
    $extension = strtolower($extension);

    return isset(self :: $mime_types[$extension])
      ? self :: $mime_types[$extension]
      : NULL;
  }
}
?>