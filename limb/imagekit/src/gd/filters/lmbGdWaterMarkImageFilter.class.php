<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/imagekit/src/lmbAbstractImageFilter.class.php');

/**
 * Resize image filter
 * @package imagekit
 * @version $Id: lmbGdWaterMarkImageFilter.class.php 7071 2008-06-25 14:33:29Z korchasa $
 */
class lmbGdWaterMarkImageFilter extends lmbAbstractImageFilter
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

  /**
   * Calculate position of a watermark
   *
   * @param int $x x position of watermark
   * @param int $y y position of watermark
   * @param int $width width of a marked image
   * @param int $height height of a marked image
   * @param mixed $wm_width width of a watermark
   * @param mixed $wm_height height of a watermark
   * @return array (x, y)
   */
  function calcPosition($x, $y, $width, $height, $wm_width = false, $wm_height = false)
  {
    if($wm_width !== false)
    {
      $x += round(($width - $wm_width) / 2);
    }
    else
    {
      if($x < 0)
        $x += $width;
    }
    if($wm_height !== false)
    {
      $y += round(($height - $wm_height) / 2);
    }
    else
    {
      if($y < 0)
        $y += $height;
    }
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

  function getXCenter()
  {
    return $this->getParam('xcenter', false);
  }

  function getYCenter()
  {
    return $this->getParam('ycenter', false);
  }
}
