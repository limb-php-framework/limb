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
 * @version $Id: lmbImageKit.class.php 7426 2008-12-19 11:53:16Z korchasa $
 */
class lmbImageKit
{
  static function create($library = 'gd', $dir = '', $params = array())
  {
    $image_class_name = 'lmb' . ucfirst($library) . 'ImageConvertor';

    $class_path = 'limb/imagekit/src/' .  $library . '/' . $image_class_name . '.class.php';

    lmb_require($class_path);

    try {
      $convertor = new $image_class_name($params);
    }
    catch (lmbException $e)
    {
      throw new lmbFileNotFoundException($class_path, 'image library not found');
    }

    return $convertor;
  }

  static function load($file_name, $type = '', $library = 'gd', $dir = '', $params = array())
  {
  	$convertor = self::create($library, $dir, $params);
    $convertor->load($file_name, $type);
    return $convertor;
  }

}
