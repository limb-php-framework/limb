<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2008 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require(dirname(__FILE__).'/../../lmbAbstractImageFilter.class.php');

/**
 * Crop image filter
 * @package imagekit
 * @version $Id: lmbGdCropImageFilter.class.php 6963 2008-04-28 04:04:31Z svk $
 */
class lmbGdCropImageFilter extends lmbAbstractImageFilter
{
  function apply(lmbAbstractImageContainer $container)
  {
    list($x, $y, $width, $height) = $this->calculateCropArea($container->getWidth(), $container->getHeight());
    $im = $container->isPallete() ? imagecreate($width, $height) : imagecreatetruecolor($width, $height);
    imagecopy($im, $container->getResource(), 0, 0, $x, $y, $width, $height);
    $container->replaceResource($im);
  }
  
  function calculateCropArea($image_width, $image_height)
  {
    $width = $this->getWidth();
    $height = $this->getHeight();
    if($width === null)
      $width = $image_width;
    if($height === null)
      $height = $image_height;
      
    $x = $this->getX();
    $y = $this->getY();
    
    if($x + $width > $image_width)
      $width -= $x + $width - $image_width;
    if($y + $height > $image_height)
      $height -= $y + $height - $image_height;
      
    return array($x, $y, $width, $height);
  }

  function getWidth()
  {
  	return $this->getParam('width');
  }

  function getHeight()
  {
    return $this->getParam('height');
  }
  
  function getX()
  {
    return $this->getParam('x', 0);
  }

  function getY()
  {
    return $this->getParam('y', 0);
  }

}