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
abstract class lmbBaseResizeImageFilter extends lmbAbstractImageFilter
{
  protected function calcNewSize($src_w, $src_h)
  {
    $dst_w = $this->getWidth();
    if(!$dst_w)
      $dst_w = $src_w;
    $dst_h = $this->getheight();
    if(!$dst_h)
      $dst_h = $src_h;

    return $this->calcSize($src_w, $src_h, $dst_w, $dst_h, $this->getPreserveAspectRatio(), $this->getSaveMinSize());
  }

  function getWidth()
  {
    return $this->getParam('width');
  }

  function getHeight()
  {
    return $this->getParam('height');
  }

  function getPreserveAspectRatio()
  {
    return $this->getParam('preserve_aspect_ratio', true);
  }

  function getSaveMinSize()
  {
    return $this->getParam('save_min_size', false);
  }
}
