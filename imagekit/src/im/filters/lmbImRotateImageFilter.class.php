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
 * @version $Id$
 */
class lmbImRotateImageFilter extends lmbBaseRotateImageFilter
{

  function apply(lmbAbstractImageContainer $container)
  {
    $angle = $this->getAngle();
    if(!$angle)
      return;
    $bgcolor = $this->colorArrayToStr($this->getBgColor());
    $image = $container->getResource();
    $image->rotateImage(new ImagickPixel($bgcolor), $angle);
    $container->replaceResource($image);
  }

  function getBgColor()
  {
    return $this->colorStrToArray($this->getParam('bgcolor', 'FFFFFF'));
  }

  protected function colorStrToArray($color_str)
  {
    return array(
      'red'   => hexdec(substr($color_str, 0, 2)),
      'green' => hexdec(substr($color_str, 2, 2)),
      'blue'  => hexdec(substr($color_str, 4, 2)),
    );
  }

  protected function colorArrayToStr($color_array)
  {
    return '#'.dechex($color_array['red']).dechex($color_array['green']).dechex($color_array['blue']);
  }
}
