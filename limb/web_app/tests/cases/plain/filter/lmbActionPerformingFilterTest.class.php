<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbActionPerformingFilterTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/filter_chain/src/lmbFilterChain.class.php');
lmb_require('limb/web_app/src/filter/lmbActionPerformingFilter.class.php');
lmb_require('limb/web_app/src/controller/lmbAbstractController.class.php');

Mock :: generate('lmbFilterChain', 'MockFilterChain');
Mock :: generate('lmbAbstractController', 'MockController');

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
?>
