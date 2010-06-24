<?php
lmb_require('limb/cache2/tests/cases/logs/lmbCacheLogTest.class.php');
lmb_require('limb/cache2/src/logs/lmbCacheLogFile.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');

class lmbCacheLogFileTest extends lmbCacheLogTest
{
  function setUp()
  {
    lmbFs::rm(LIMB_VAR_DIR);
    lmbFs::mkdir(LIMB_VAR_DIR);

    $this->logger = new lmbCacheLogFile(LIMB_VAR_DIR.'/cache.log');

    parent::setUp();
  }

  function tearDown()
  {
    unset($this->logger);
    lmbFs::rm(LIMB_VAR_DIR);
    parent::tearDown();
  }
}
