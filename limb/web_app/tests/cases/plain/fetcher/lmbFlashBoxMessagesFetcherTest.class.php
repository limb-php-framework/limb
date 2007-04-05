<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFlashBoxMessagesFetcherTest.class.php 5532 2007-04-05 10:31:47Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/fetcher/lmbFlashBoxMessagesFetcher.class.php');
lmb_require('limb/web_app/tests/cases/lmbWebAppTestCase.class.php');

class lmbFlashBoxMessagesFetcherTest extends lmbWebAppTestCase
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
    $this->toolkit->getFlashBox()->addMessage('Message1');
    $this->toolkit->getFlashBox()->addMessage('Message2');

    $fetcher = new lmbFlashBoxMessagesFetcher();
    $rs = $fetcher->fetch();

    $rs->rewind();
    $this->assertEqual($rs->current()->get('message'), 'Message1');
    $rs->next();
    $this->assertEqual($rs->current()->get('message'), 'Message2');
  }

  function testFetcherResetsMessagesList()
  {
    $this->toolkit->getFlashBox()->addMessage('Message1');
    $this->toolkit->getFlashBox()->addMessage('Message2');

    $fetcher = new lmbFlashBoxMessagesFetcher();
    $rs = $fetcher->fetch();

    $rs = $fetcher->fetch();
    $rs->rewind();
    $this->assertFalse($rs->valid());
  }
}
?>
