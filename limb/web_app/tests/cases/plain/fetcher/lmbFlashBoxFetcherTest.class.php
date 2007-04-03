<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFlashBoxFetcherTest.class.php 5431 2007-03-29 15:33:42Z serega $
 * @package    web_app
 */
lmb_require('limb/web_app/src/fetcher/lmbFlashBoxFetcher.class.php');
lmb_require('limb/web_app/tests/cases/lmbWebAppTestCase.class.php');

class lmbFlashBoxFetcherTest extends lmbWebAppTestCase
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

  function testGetDatasetErrorsComeFirst()
  {
    $this->toolkit->getFlashBox()->addMessage('Message1');
    $this->toolkit->getFlashBox()->addError('Error2');

    $fetcher = new lmbFlashBoxFetcher();
    $rs = $fetcher->fetch();

    $rs->rewind();
    $this->assertFalse($rs->current()->get('is_message'));
    $this->assertTrue($rs->current()->get('is_error'));
    $this->assertEqual($rs->current()->get('text'), 'Error2');

    $rs->next();
    $this->assertTrue($rs->current()->get('is_message'));
    $this->assertFalse($rs->current()->get('is_error'));
    $this->assertEqual($rs->current()->get('text'), 'Message1');

  }

  function testFetcherResetsMessagesList()
  {
    $this->toolkit->getFlashBox()->addMessage('Message1');
    $this->toolkit->getFlashBox()->addError('Error2');

    $fetcher = new lmbFlashBoxFetcher();
    $rs = $fetcher->fetch();

    $rs = $fetcher->fetch();
    $rs->rewind();
    $this->assertFalse($rs->valid());
  }
}
?>
