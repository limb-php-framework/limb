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
 * @version $Id: lmbImageKit.class.php 7071 2008-06-25 14:33:29Z korchasa $
 */
class lmbImageKit
{

  static function create($library = 'gd', $dir = '', $params = array())
  {
    if(defined('LIMB_IMAGE_LIBRARY'))
      $library = LIMB_IMAGE_LIBRARY;

    $image_class_name = 'lmb' . ucfirst($library) . 'ImageConvertor';

    $class_path = 'limb/imagekit/src/' .  $library . '/' . $image_class_name . '.class.php';

    if(!file_exists($class_path))
      throw new lmbFileNotFoundException($class_path, 'image library not found');

    lmb_require($class_path);

    return new $image_class_name($params);
  }

  static function load($file_name, $type = '', $library = 'gd', $dir = '', $params = array())
  {
  	$convertor = self::create($library, $dir, $params);
    $convertor->load($file_name, $type);
    return $convertor;
  }

}
