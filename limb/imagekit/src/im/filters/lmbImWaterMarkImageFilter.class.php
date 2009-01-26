<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/imagekit/src/lmbAbstractImageFilter.class.php');

/**
 * Watermark image filter
 * @package imagekit
 * @version $Id$
 */
class lmbImWaterMarkImageFilter extends lmbAbstractImageFilter
{

  function apply(lmbAbstractImageContainer $container)
  {
    $width = $container->getWidth();
    $height = $container->getHeight();
    $wm_cont = new Imagick();
    $wm_cont->readImage($this->getWaterMark());
    list($x, $y) = $this->calcPosition($this->getX(), $this->getY(), $width, $height);
    $container->getResource()->compositeImage($wm_cont, Imagick::COMPOSITE_OVER, $x, $y, Imagick::CHANNEL_ALL);
  }

  function calcPosition($x, $y, $width, $height)
  {
  	if($x >= 0 && $y >= 0)
      return array($x, $y);
    if($x < 0)
      $x += $width;
    if($y < 0)
      $y += $height;
    return array($x, $y);
  }

  function getWaterMark()
  {
  	return $this->getParam('water_mark');
  }

  function getX()
  {
    return $this->getParam('x', 0);
  }

  function getY()
  {
    return $this->getParam('y', 0);
  }

  function getOpacity()
  {
    return $this->getParam('opacity', 0);
  }
}
