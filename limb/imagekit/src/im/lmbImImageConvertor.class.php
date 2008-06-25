<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/imagekit/src/lmbAbstractImageConvertor.class.php');
lmb_require('limb/imagekit/src/im/lmbImImageContainer.class.php');
lmb_require('limb/fs/src/exception/lmbFileNotFoundException.class.php');
lmb_require('limb/imagekit/src/exception/lmbImageLibraryNotInstalledException.class.php');

/**
 * Imagick image convertor
 *
 * @package imagekit
 * @version $Id: lmbImImageConvertor.class.php 7071 2008-06-25 14:33:29Z korchasa $
 */
class lmbImImageConvertor extends lmbAbstractImageConvertor
{

  function __construct($params = array())
  {
    if (!class_exists('Imagick'))
      throw new lmbImageLibraryNotInstalledException('ImageMagick');

    if(!isset($params['filters_scan_dirs']))
      $params['filters_scan_dirs'] = 'limb/imagekit/src/im/filters';
    parent::__construct($params);
  }

  protected function createFilter($name, $params)
  {
    $class = $this->loadFilter($name, 'Im');
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
