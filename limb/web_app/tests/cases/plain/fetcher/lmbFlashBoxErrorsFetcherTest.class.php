<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_app/src/fetcher/lmbFlashBoxErrorsFetcher.class.php');
lmb_require('limb/web_app/tests/cases/lmbWebAppTestCase.class.php');

class lmbFlashBoxErrorsFetcherTest extends lmbWebAppTestCase
{
  function setUp()
  {
    parent :: setUp();
    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
    parent :: tearDown();
  }

  function _cleanUp()
  {
     $this->toolkit->getFlashBox()->reset();
  }

  function testFetch()
  {
    $this->toolkit->getFlashBox()->addError('Error1');
    $this->toolkit->getFlashBox()->addError('Error2');

    $fetcher = new lmbFlashBoxErrorsFetcher();
    $rs = $fetcher->fetch();

    $rs->rewind();
    $this->assertEqual($rs->current()->get('message'), 'Error1');
    $rs->next();
    $this->assertEqual($rs->current()->get('message'), 'Error2');
  }

  function testFetcherResetsErrorsList()
  {
    $this->toolkit->getFlashBox()->addError('Error1');
    $this->toolkit->getFlashBox()->addError('Error2');

    $fetcher = new lmbFlashBoxErrorsFetcher();
    $rs = $fetcher->fetch();

    $rs = $fetcher->fetch();
    $rs->rewind();
    $this->assertFalse($rs->valid());
  }
}
?>
