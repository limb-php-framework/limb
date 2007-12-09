<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require(dirname(__FILE__).'/../lmbAbstractImageConvertor.class.php');
lmb_require(dirname(__FILE__).'/lmbImImageContainer.class.php');
lmb_require('limb/fs/src/exception/lmbFileNotFoundException.class.php');
lmb_require('limb/imagekit/src/exception/lmbLibraryNotInstalledException.class.php');


/**
 * Imagick image convertor
 *
 * @package imagekit
 * @version $Id$
 */
class lmbImImageConvertor extends lmbAbstractImageConvertor
{
  
  function __construct()
  {
    if (!class_exists('Imagick'))
      throw new lmbLibraryNotInstalledException('ImageMagick');
  }
  
  protected function createFilter($name, $params)
  {
    $class = $this->loadFilter(dirname(__FILE__).'/filters', $name, 'Im');
    return new $class($params);
  }

  protected function createImageContainer($file_name, $type = '')
  {
    $container = new lmbImImageContainer();
    $container->load($file_name, $type);
    return $container;
  }

  function isSupportConversion($file, $src_type = '', $dest_type = '')
  {
    if(!$src_type)
    {
      $imginfo = getimagesize($file);
      if(!$imginfo)
        throw new lmbFileNotFoundException($file);
      $src_type = lmbImImageContainer::convertImageType($imginfo[2]);
    }
    if(!$dest_type)
      $dest_type = $src_type;
    return lmbImImageContainer::supportLoadType($src_type) &&
           lmbImImageContainer::supportSaveType($dest_type);
  }
}
