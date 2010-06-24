<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/imagekit/src/filters/lmbBaseRotateImageFilter.class.php');

/**
 * Rotate image filter
 * @package imagekit
 * @version $Id: lmbGdRotateImageFilter.class.php 8152 2010-03-29 00:04:37Z korchasa $
 */
class lmbGdRotateImageFilter extends lmbBaseRotateImageFilter
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

  function getBgColor()
  {
    $bgcolor = $this->getParam('bgcolor', 'FFFFFF');
    return $this->parseHexColor($bgcolor);
  }
}
