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
 * @version $Id: lmbGdRotateImageFilter.class.php 6333 2007-09-24 16:38:22Z cmz $
 */
lmb_require(dirname(__FILE__).'/../../lmbAbstractImageFilter.class.php');

/**
 * Rotate image filter
 * @package imagekit
 * @version $Id: lmbGdRotateImageFilter.class.php 6333 2007-09-24 16:38:22Z cmz $
 */
class lmbGdRotateImageFilter extends lmbAbstractImageFilter
{

  function apply(lmbAbstractImageContainer $container)
  {
    $angle = $this->getAngle();
    if(!$angle)
      return;
    $bgcolor = $this->getBgColor();
    $cur_im = $container->getResource();
    $bg = imagecolorallocate($cur_im, $bgcolor['red'], $bgcolor['green'], $bgcolor['blue']);
    $im = imagerotate($cur_im, $angle, $bg);
    $container->replaceResource($im);
  }

  function getAngle()
  {
    return $this->getParam('angle', 0);
  }

  function getBgColor()
  {
    $bgcolor = $this->getParam('bgcolor', 'FFFFFF');
    return $this->parseHexColor($bgcolor);
  }
}
?>