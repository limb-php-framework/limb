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
 * @version $Id: lmbGdCropImageFilter.class.php 8152 2010-03-29 00:04:37Z korchasa $
 */
class lmbGdCropImageFilter extends lmbBaseCropImageFilter
{
  function apply(lmbAbstractImageContainer $container)
  {
    list($x, $y, $width, $height) = $this->calculateCropArea($container->getWidth(), $container->getHeight());
    $im = $container->isPallete() ? imagecreate($width, $height) : imagecreatetruecolor($width, $height);
    imagecopy($im, $container->getResource(), 0, 0, $x, $y, $width, $height);
    $container->replaceResource($im);
  }
}