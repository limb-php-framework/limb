<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/imagekit/src/lmbAbstractImageContainer.class.php');

/**
 * Abstract image filter
 *
 * @package imagekit
 * @version $Id: lmbAbstractImageFilter.class.php 8065 2010-01-20 04:18:19Z korchasa $
 */
abstract class lmbAbstractImageFilter
{
  protected $params;

  function __construct($params)
  {
    $this->params = $params;
  }

  function parseHexColor($hex)
  {
    $length = strlen($hex);
    $color['red'] = hexdec(substr($hex, $length - 6, 2));
    $color['green'] = hexdec(substr($hex, $length - 4, 2));
    $color['blue'] = hexdec(substr($hex, $length - 2, 2));
    return $color;
  }

  function calcSize($src_w, $src_h, $dst_w, $dst_h, $preserve_aspect_ratio = true, $save_min_size = false)
  {
    $w = $dst_w;
    $h = $dst_h;
    if($preserve_aspect_ratio)
    {
      $scale = (float)1;
      $scale_w = (float)$dst_w / (float)$src_w;
      $scale_h = (float)$dst_h / (float)$src_h;
      if($scale_w > 1 && $scale_h > 1)
      {
        if($save_min_size)
          $scale = 1;
        elseif($scale_w > $scale_h)
          $scale = $scale_h;
        else
          $scale = $scale_w;
      }
      elseif($scale_w < 1 && $scale_h < 1)
      {
        if($scale_w > $scale_h)
          $scale = $scale_h;
        else
          $scale = $scale_w;
      }
      elseif($scale_w < 1)
        $scale = $scale_w;
      else
        $scale = $scale_h;
      $w = (int) round($src_w * $scale);
      $h = (int) round($src_h * $scale);
    }
    elseif($save_min_size)
    {
      if($dst_w > $src_w)
        $w = $src_w;
      if($dst_h > $src_h)
        $h = $src_h;
    }
    return array($w, $h);
  }

  function getParam($name, $default = null)
  {
    $param = $default;
    if(isset($this->params[$name]))
      $param = $this->params[$name];
    return $param;
  }

  abstract function apply(lmbAbstractImageContainer $container);
}
