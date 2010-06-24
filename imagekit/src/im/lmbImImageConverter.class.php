<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/imagekit/src/lmbAbstractImageConverter.class.php');
lmb_require('limb/imagekit/src/im/lmbImImageContainer.class.php');
lmb_require('limb/fs/src/exception/lmbFileNotFoundException.class.php');
lmb_require('limb/imagekit/src/exception/lmbImageLibraryNotInstalledException.class.php');

/**
 * Imagick image converter
 *
 * @package imagekit
 * @version $Id: lmbImImageConverter.class.php 8110 2010-01-28 14:20:12Z korchasa $
 */
class lmbImImageConverter extends lmbAbstractImageConverter
{

  function __construct($params = array())
  {
    if (!class_exists('Imagick'))
      throw new lmbImageLibraryNotInstalledException('ImageMagick');

    if(!isset($params['filters_scan_dirs']))
      $params['filters_scan_dirs'] = dirname(__FILE__) . '/filters';
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
