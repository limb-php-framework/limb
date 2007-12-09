<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package imagekit
 * @version $Id: lmbImageKit.class.php 6607 2007-12-09 15:21:52Z svk $
 */
class lmbImageKit
{

  static function create($library = 'gd', $dir = '')
  {
    if(defined('LIMB_IMAGE_LIBRARY'))
      $library = LIMB_IMAGE_LIBRARY;

    $image_class_name = 'lmb' . ucfirst($library) . 'ImageConvertor';

    $class_path = dirname(__FILE__) .  '/'.  $library . '/' . $image_class_name . '.class.php';

    if(!file_exists($class_path))
      throw new lmbFileNotFoundException($class_path, 'image library not found');

    lmb_require($class_path);

    return new $image_class_name();
  }

  static function load($file_name, $type = '', $library = 'gd', $dir = '')
  {
  	$convertor = self::create($library, $dir);
    $convertor->load($file_name, $type);
    return $convertor;
  }

}
