<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_app/src/request/lmbCompositeRequestDispatcher.class.php');
lmb_require('limb/web_app/src/request/lmbRequestDispatcher.interface.php');
lmb_require('limb/net/src/lmbHttpRequest.class.php');

Mock :: generate('lmbRequestDispatcher', 'MockRequestDispatcher');

class lmbCompositeRequestDispatcherTest extends UnitTestCase
{
  protected $request;

  function setUp()
  {
    $this->request = new lmbHttpRequest();
  }

  protected function _setUpMocks($dispatcher, $result1, $result2)
  {
    $mock_dispatcher1 = new MockRequestDispatcher();
    $mock_dispatcher2 = new MockRequestDispatcher();

    $dispatcher->addDispatcher($mock_dispatcher1);
    $dispatcher->addDispatcher($mock_dispatcher2);

    $this->_setUpMockDispatcher($mock_dispatcher1, $result1);
    $this->_setUpMockDispatcher($mock_dispatcher2, $result2);
  }

  protected function _setUpMockDispatcher($mock_dispatcher, $result)
  {
    if($result !== null)
    {
      $mock_dispatcher->expectOnce('dispatch', array($this->request));
      $mock_dispatcher->setReturnValue('dispatch', $result);
    }
    else
      $mock_dispatcher->expectNever('dispatch');
  }

  function testDispatchOkByFirstDispatcher()
  {
    $dispatcher = new lmbCompositeRequestDispatcher();
    $this->_setUpMocks($dispatcher, $result = array('controller' => 'whatever'), null);
    $this->assertEqual($dispatcher->dispatch($this->request), $result);
  }

  function testDispatchOkBySecondDispatcherSinceFirstReturnNoController()
  {
    $dispatcher = new lmbCompositeRequestDispatcher();
    $this->_setUpMocks($dispatcher,
                       array('any_param' => 'whatever'),
                       $result = array('controller' => 'whatever'));
    $this->assertEqual($dispatcher->dispatch($this->request), $result);
  }

  function testReturnEmptyArraySinceAllDispatchersCantDispatchController()
  {
    $dispatcher = new lmbCompositeRequestDispatcher();
    $this->_setUpMocks($dispatcher,
                       array('any_param1' => 'whatever'),
                       array('any_param1' => 'anything'));
    $this->assertEqual($dispatcher->dispatch($this->request), array());
  }
}


