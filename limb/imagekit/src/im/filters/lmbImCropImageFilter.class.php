<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2008 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/imagekit/src/filters/lmbBaseCropImageFilter.class.php');

/**
 * Crop image filter
 * @package imagekit
 * @version $Id: $
 */
class lmbImCropImageFilter extends lmbBaseCropImageFilter
{
  function apply(lmbAbstractImageContainer $container)
  {
    list($x, $y, $width, $height) = $this->calculateCropArea($container->getWidth(), $container->getHeight());
    $container->getResource()->cropImage($width, $height, $x, $y);
  }

}