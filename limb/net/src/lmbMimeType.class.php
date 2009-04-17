<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbMimeType.
 *
 * @package net
 * @version $Id: lmbMimeType.class.php 7886 2009-04-17 07:39:04Z serega $
 */
class lmbMimeType
{
  static protected $mime_types = array(
    'video/avi'=>'avi',
    'application/x-flash-video'=>'flv',
    'audio/x-aiff'=>'aif',
    'audio/x-aiff' => 'aifc',
    'audio/x-aiff' => 'aiff',
    'image/bmp' =>'bmp',
    'application/msword' =>'doc',
    'video/x-flv' =>'flv',
    'image/gif' =>'gif',
    'text/html' =>'html',
    'image/pjpeg' =>'jpeg',
    'image/jpeg' =>'jpg',
    'text/javascript' =>'js',
    'video/mpeg' =>'mpeg',
    'audio/mpeg' =>'mp3',
    'video/mpeg' =>'mpg',
    'message/rfc822' =>'msg',
    'application/pdf' =>'pdf',
    'image/png' =>'png',
    'image/x-png' => 'png',
    'application/vnd.ms-powerpoint' =>'ppt',
    'image/psd' =>'psd',
    'text/rtf' =>'rtf',
    'application/x-shockwave-flash' =>'swf',
    'text/plain' =>'txt',
    'audio/x-wav' =>'wav',
    'application/vnd.ms-excel' =>'xls',
    'application/x-rar-compressed' =>'rar',
    'application/rar' =>'rar',
    'application/x-zip-compressed' =>'zip',
    'application/zip' =>'zip'
  );

  static function getExtension($mime_type)
  {
    $mime_type = strtolower($mime_type);
    return isset(self :: $mime_types[$mime_type])
      ? self :: $mime_types[$mime_type]
      : null;
  }

  static function getMimeType($extension)
  {

    $extension = ltrim(strtolower($extension), '.');
    $mime_type = array_search($extension,self :: $mime_types);
    
    return $mime_type ? $mime_type : null;
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

