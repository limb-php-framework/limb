<?php
lmb_require('limb/cache2/tests/cases/drivers/lmbCacheConnectionTest.class.php');

class lmbTaggableCacheWrapperBcTest extends lmbCacheConnectionTest
{
  function __construct()
  {
    $dir = lmb_var_dir() . '/cache';
    $this->dsn = 'file:///' . $dir . '?wrapper[]=taggable';
  }
}
