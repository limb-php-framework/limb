<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCacheMemoryPersisterTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/cache/src/lmbCacheMemoryPersister.class.php');
lmb_require(dirname(__FILE__) . '/lmbCacheTestBase.class.php');

class lmbCacheMemoryPersisterTest extends lmbCacheTestBase
{
  function _createPersisterImp()
  {
    return new lmbCacheMemoryPersister();
  }
}

?>