<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
class lmbImageFactory
{
  function create($library = 'gd', $dir = '')
  {
    if(defined('LIMB_IMAGE_LIBRARY'))
      $library = LIMB_IMAGE_LIBRARY;

    $image_class_name = 'lmbImage' . ucfirst($library);

    if(isset($GLOBALS['global_' . $image_class_name]))
      $obj = $GLOBALS['global_' . $image_class_name];
    else
      $obj = null;

    if(get_class($obj) != $image_class_name)
    {
      $dir = ($dir == '') ? 'limb/imagekit/src/' : $dir;

      if(!file_exists(dirname(__FILE__) .  '/'.  $image_class_name . '.class.php'))
        throw new lmbFileNotFoundException(dirname(__FILE__) .  '/'. $image_class_name . '.class.php', 'image library not found');

      lmb_require(dirname(__FILE__) .  '/'. $image_class_name . '.class.php');

      $obj = new $image_class_name();
      $GLOBALS['global_' . $image_class_name] = $obj;
    }

    return $obj;
  }

}
?>