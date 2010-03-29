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
 * Base class for resize image filter
 * @package imagekit
 * @version $Id:$
 */
abstract class lmbBaseWaterMarkImageFilter extends lmbAbstractImageFilter
{
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
      $x += round(($width - $wm_width) / 2);
    else
      if($x < 0)
        $x += $width;

    if($wm_height !== false)
      $y += round(($height - $wm_height) / 2);
    else
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

  function getXCenter()
  {
    return $this->getParam('xcenter', false);
  }

  function getYCenter()
  {
    return $this->getParam('ycenter', false);
  }
}
