<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/filter_chain/src/lmbFilterChain.class.php');
lmb_require('limb/net/src/lmbHttpResponse.class.php');
lmb_require('limb/web_app/src/filter/lmbViewRenderingFilter.class.php');
lmb_require('limb/view/src/lmbView.class.php');

Mock :: generate('lmbHttpResponse', 'MockHttpResponse');
Mock :: generate('lmbFilterChain', 'MockFilterChain');
Mock :: generate('lmbView', 'MockView');

class lmbViewRenderingFilterTest extends UnitTestCase
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

  function testRenderViewIfResponseEmpty()
  {
    $response = new MockHttpResponse();
    $response->expectOnce('isEmpty');
    $response->setReturnValue('isEmpty', true);
    $this->toolkit->setResponse($response);

    $view = new MockView();
    $this->toolkit->setView($view);

    $filter = new lmbViewRenderingFilter();

    $view->expectOnce('render');
    $view->setReturnValue('render', 'bar');
    $response->expectOnce('write', array('bar'));

    $chain = new MockFilterChain();
    $chain->expectOnce('next');

    $filter->run($chain);
  }

  function testDoNotRenderViewIfResponseNotEmpty()
  {
    $response = new MockHttpResponse();
    $response->expectOnce('isEmpty');
    $response->setReturnValue('isEmpty', false);
    $this->toolkit->setResponse($response);

    $view = new MockView();
    $this->toolkit->setView($view);

    $filter = new lmbViewRenderingFilter();

    $view->expectNever('render');
    $response->expectNever('write');

    $chain = new MockFilterChain();
    $chain->expectOnce('next');

    $filter->run($chain);
  }
}


