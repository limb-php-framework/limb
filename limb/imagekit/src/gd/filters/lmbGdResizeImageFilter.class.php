<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/imagekit/src/filters/lmbBaseResizeImageFilter.class.php');

/**
 * Resize image filter
 * @package imagekit
 * @version $Id: lmbGdResizeImageFilter.class.php 8152 2010-03-29 00:04:37Z korchasa $
 */
class lmbGdResizeImageFilter extends lmbBaseResizeImageFilter
{
  function apply(lmbAbstractImageContainer $container)
  {
    $src_w = $container->getWidth();
    $src_h = $container->getHeight();
    list($dst_w, $dst_h) = $this->calcNewSize($src_w, $src_h);
    $im = imagecreatetruecolor($dst_w, $dst_h);
    imagecopyresampled($im, $container->getResource(), 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
    $container->replaceResource($im);
  }
}
