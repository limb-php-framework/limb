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
 * Resize image filter
 * @package imagekit
 * @version $Id: lmbGdWaterMarkImageFilter.class.php 8152 2010-03-29 00:04:37Z korchasa $
 */
class lmbGdWaterMarkImageFilter extends lmbBaseWaterMarkImageFilter
{

  function apply(lmbAbstractImageContainer $container)
  {
    $width = $container->getWidth();
    $height = $container->getHeight();
    $wm_cont = new lmbGdImageContainer();
    $wm_cont->load($this->getWaterMark());
    $wm_width = $this->getXCenter() ? $wm_cont->getWidth() : false;
    $wm_height = $this->getYCenter() ? $wm_cont->getHeight() : false;
    list($x, $y) = $this->calcPosition($this->getX(), $this->getY(), $width, $height, $wm_width, $wm_height);
    imagecopymerge($container->getResource(), $wm_cont->getResource(), $x, $y, 0, 0, $wm_cont->getWidth(), $wm_cont->getHeight(), 100 - $this->getOpacity());
  }
}
