<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require(dirname(__FILE__).'/../lmbAbstractImageConvertor.class.php');
lmb_require(dirname(__FILE__).'/lmbGdImageContainer.class.php');
lmb_require('limb/fs/src/exception/lmbFileNotFoundException.class.php');
lmb_require('limb/imagekit/src/exception/lmbImageLibraryNotInstalledException.class.php');

/**
 * GD image convertor
 *
 * @package imagekit
 * @version $Id: lmbGdImageConvertor.class.php 6963 2008-04-28 04:04:31Z svk $
 */
class lmbGdImageConvertor extends lmbAbstractImageConvertor
{

  function __construct($params = array())
  {
    if (!function_exists('gd_info'))
      throw new lmbImageLibraryNotInstalledException('gd');
      
    if(!isset($params['filters_scan_dirs']))
      $params['filters_scan_dirs'] = dirname(__FILE__).'/filters';
    parent::__construct($params);
  }
    
  protected function createFilter($name, $params)
  {
    $class = $this->loadFilter($name, 'Gd', $params);
    return new $class($params);
  }

  protected function createImageContainer($file_name, $type = '')
  {
    $container = new lmbGdImageContainer();
    $container->load($file_name, $type);
    return $container;
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
