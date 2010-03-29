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
 * @version $Id$
 */
class lmbImResizeImageFilter extends lmbBaseResizeImageFilter
{
  const RESIZE_TYPE_FIT = 1;
  const RESIZE_TYPE_CUT = 2;

  function apply(lmbAbstractImageContainer $container)
  {
    $src_w = $container->getWidth();
    $src_h = $container->getHeight();
    list($dst_w, $dst_h) = $this->calcNewSize($src_w, $src_h);
    $image = $container->getResource();
    $image->thumbnailImage($dst_w, $dst_h, false);
    $container->replaceResource($image);
  }
}
