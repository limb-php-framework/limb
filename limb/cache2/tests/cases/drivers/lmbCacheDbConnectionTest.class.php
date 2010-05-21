<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cache2/src/drivers/lmbCacheDbConnection.class.php');
lmb_require(dirname(__FILE__) . '/lmbCacheConnectionTest.class.php');
require_once('limb/dbal/tests/cases/init.inc.php');

class lmbCacheDbConnectionTest extends lmbCacheConnectionTest
{
  protected $storage_init_file = 'limb/dbal/common.inc.php';
  protected $fixture_path;

  function __construct()
  {
    $this->dsn = 'db://dsn?table=lmb_cache2';
    $this->fixture_path = dirname(__FILE__) . '/../../../init/cache.';

    lmb_tests_init_db_dsn();
    lmb_tests_setup_db($this->fixture_path);

    parent::__construct();
  }

  function skip()
  {
    $this->skipIf(
      lmb_tests_db_dump_does_not_exist($this->fixture_path, 'CACHE2'),
      'lmbCacheDbConnection test skipped (no fixture found).'
    );
  }

  function __destruct()
  {
    if (!$this->_should_skip)
      lmb_tests_teardown_db();
  }
}
