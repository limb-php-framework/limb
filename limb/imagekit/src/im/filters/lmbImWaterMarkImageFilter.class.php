<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/imagekit/src/filters/lmbBaseWaterMarkImageFilter.class.php');

/**
 * Watermark image filter
 * @package imagekit
 * @version $Id$
 */
class lmbImWaterMarkImageFilter extends lmbBaseWaterMarkImageFilter
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
}
