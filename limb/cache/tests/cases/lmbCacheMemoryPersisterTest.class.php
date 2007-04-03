<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCacheMemoryPersisterTest.class.php 4985 2007-02-08 15:35:06Z pachanga $
 * @package    cache
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