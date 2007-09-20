<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package imagekit
 * @version $Id$
 */
lmb_require(dirname(__FILE__).'/../../lmbAbstractImageFilter.class.php');

/**
 * Resize image filter
 * @package imagekit
 * @version $Id$
 */
class lmbGdWaterMarkImageFilter extends lmbAbstractImageFilter
{

  function run(lmbAbstractImageContainer $container)
  {
    $width = $container->getWidth();
    $height = $container->getHeight();
    $wm_cont = new lmbGdImageContainer($this->getWaterMark());
    list($x, $y) = $this->calcPosition($this->getX(), $this->getY(), $width, $height);
    imagecopymerge($container->getResource(), $wm_cont->getResource(), $x, $y, 0, 0, $wm_cont->getWidth(), $wm_cont->getHeight(), 100 - $this->getOpacity());
  }

  function calcPosition($x, $y, $width, $height)
  {
  	if($x >= 0 && $y >= 0) return array($x, $y);
    if($x < 0) $x += $width;
    if($y < 0) $y += $height;
    return array($x, $y);
  }

  function getWaterMark()
  {
  	return $this->getParam('water_mark', 0);
  }

  function getX()
  {
    return $this->getParam('x', 1, 0);
  }

  function getY()
  {
    return $this->getParam('y', 2, 0);
  }

  function getOpacity()
  {
    return $this->getParam('opacity', 3, 0);
  }
}
?>