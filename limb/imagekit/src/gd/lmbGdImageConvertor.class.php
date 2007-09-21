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
 * @version $Id$
 */
lmb_require(dirname(__FILE__).'/../lmbAbstractImageConvertor.class.php');
lmb_require(dirname(__FILE__).'/lmbGdImageContainer.class.php');
lmb_require('limb/fs/src/exception/lmbFileNotFoundException.class.php');

/**
 * GD image convertor
 *
 * @package imagekit
 * @version $Id$
 */
class lmbGdImageConvertor extends lmbAbstractImageConvertor 
{
  protected function createFilter($name, $params)
  {
    $class = $this->loadFilter(dirname(__FILE__).'/filters', $name, 'Gd');
    return new $class($params);
  }

  protected function createImageContainer($file_name, $type = '')
  {
    return new lmbGdImageContainer($file_name, $type);
  }

  function isSupportConversion($file, $src_type = '', $dest_type = '')
  {
    if(!$src_type) 
    {
      $imginfo = @getimagesize($file);
      if(!$imginfo)
        throw new lmbFileNotFoundException($file);
      $src_type = lmbGdImageContainer::convertImageType($imginfo[2]);
    }
    if(!$dest_type)
      $dest_type = $src_type;
    return lmbGdImageContainer::supportLoadType($src_type) &&
           lmbGdImageContainer::supportSaveType($dest_type);
  }
}
?>
