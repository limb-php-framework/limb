<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/query/lmbSelectQuery.class.php');

class lmbSelectQueryTest extends UnitTestCase
{
  function testConstruct()
  {
    $sql = new lmbSelectQuery('foo');

    $this->assertEqual($sql->getTables(), array('foo'));
  }

}

