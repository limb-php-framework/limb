<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/filter_chain/src/lmbFilterChain.class.php');
lmb_require('limb/web_app/src/filter/lmbActionPerformingFilter.class.php');
lmb_require('limb/web_app/src/controller/lmbController.class.php');

Mock :: generate('lmbFilterChain', 'MockFilterChain');
Mock :: generate('lmbController', 'MockController');

class lmbActionPerformingFilterTest extends UnitTestCase
{
  var $toolkit;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testThrowExceptionIfNoDispatchedController()
  {
    $filter = new lmbActionPerformingFilter();

    $fc = new MockFilterChain();
    $fc->expectNever('next');

    try
    {
      $filter->run($fc);
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testRunOk()
  {
    $controller = new MockController();
    $controller->expectOnce('performAction');

    $this->toolkit->setDispatchedController($controller);

    $filter = new lmbActionPerformingFilter();

    $fc = new MockFilterChain();
    $fc->expectOnce('next');

    $filter->run($fc);
  }
}

