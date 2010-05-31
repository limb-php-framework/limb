<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/cache2/src/drivers/lmbCacheMemoryConnection.class.php');
lmb_require('limb/cache2/tests/cases/drivers/lmbCacheConnectionTest.class.php');

class lmbCacheCombinationMintOverTaggableTes// extends lmbCacheConnectionTest
{
  function __construct()
  {
    $this->dsn = 'memory:/?wrapper[]=taggable&wrapper[]=mint';
  }
}
