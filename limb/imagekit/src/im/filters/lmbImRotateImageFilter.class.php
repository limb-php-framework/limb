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
 * Rotate image filter
 * @package imagekit
 * @version $Id$
 */
class lmbImRotateImageFilter extends lmbAbstractImageFilter
{

  function apply(lmbAbstractImageContainer $container)
  {
    $angle = $this->getAngle();
    if(!$angle)
      return;
    $bgcolor = $this->getBgColor();
    $image = $container->getResource();
    $image->rotateImage(new ImagickPixel("#".$bgcolor), $angle);
    $container->replaceResource($image);
  }

  function getAngle()
  {
    return $this->getParam('angle', 0);
  }

  function getBgColor()
  {
    return $this->getParam('bgcolor', 'FFFFFF');
  }
}
