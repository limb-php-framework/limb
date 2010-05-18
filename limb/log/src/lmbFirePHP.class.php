<?php
lmb_require('limb/log/lib/FirePHPCore/FirePHP.class.php');

class lmbFirePHP extends FirePHP
{
  protected function setHeader($header)
  {
    lmbToolkit::instance()->getResponse()->addHeader($header);
  }
}