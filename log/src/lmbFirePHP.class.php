<?php
lmb_require('limb/log/lib/FirePHP/lib/FirePHPCore/FirePHP.class.php');

/**
 * class lmbFirePHP. 
 * 
 * Use limb method addHeader in setHeader method.
 */
class lmbFirePHP extends FirePHP
{
  /**
   * Send header
   *
   * @param string $name
   * @param string $value
   */
  protected function setHeader($name, $value)
  {
    return lmbToolkit::instance()->getResponse()->addHeader($name . ': ' . $value);
  }
}