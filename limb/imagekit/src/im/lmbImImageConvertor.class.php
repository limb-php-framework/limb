<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('imagekit/src/lmbAbstractImageConvertor.class.php');
lmb_require('imagekit/src/im/lmbImImageContainer.class.php');
lmb_require('fs/src/exception/lmbFileNotFoundException.class.php');
lmb_require('imagekit/src/exception/lmbImageLibraryNotInstalledException.class.php');

/**
 * Imagick image convertor
 *
 * @package imagekit
 * @version $Id: lmbImImageConvertor.class.php 8065 2010-01-20 04:18:19Z korchasa $
 */
class lmbImImageConvertor extends lmbAbstractImageConvertor
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
