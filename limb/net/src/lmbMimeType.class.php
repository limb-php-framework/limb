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
 * @version $Id: lmbMimeType.class.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class lmbMimeType
{
  static protected $mime_types = array(
    'avi' => 'video/avi',
    'aif' => 'audio/x-aiff',
    'aifc' => 'audio/x-aiff',
    'aiff' => 'audio/x-aiff',
    'bmp' => 'image/bmp',
    'doc' => 'application/msword',
    'flv' => 'video/x-flv',
    'gif' => 'image/gif',
    'html' => 'text/html',
    'jpeg' => 'image/pjpeg',
    'jpg' => 'image/jpeg',
    'js' => 'text/javascript',
    'mpeg' => 'video/mpeg',
    'mp3' => 'audio/mpeg',
    'mpg' => 'video/mpeg',
    'msg' => 'message/rfc822',
    'pdf' => 'application/pdf',
    'png' => 'image/png',
    'ppt' => 'application/vnd.ms-powerpoint',
    'psd' => 'image/psd',
    'rtf' => 'text/rtf',
    'swf' => 'application/x-shockwave-flash',
    'txt' => 'text/plain',
    'wav' => 'audio/x-wav',
    'xls' => 'application/vnd.ms-excel',
    'zip' => 'application/x-zip-compressed',
    'zip' => 'application/zip',
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

