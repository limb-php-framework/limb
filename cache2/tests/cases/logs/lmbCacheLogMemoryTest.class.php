<?php
lmb_require('limb/cache2/tests/cases/logs/lmbCacheLogTest.class.php');
lmb_require('limb/cache2/src/logs/lmbCacheLogMemory.class.php');

class lmbCacheLogMemoryTest extends lmbCacheLogTest
{
  function setUp()
  {
    $this->logger = new lmbCacheLogMemory();
    parent::setUp();
  }
}
