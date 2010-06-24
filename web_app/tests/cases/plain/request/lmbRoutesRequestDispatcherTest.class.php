<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_app/src/request/lmbRoutesRequestDispatcher.class.php');
lmb_require('limb/web_app/src/request/lmbRoutes.class.php');

class lmbRoutesRequestDispatcherTest extends UnitTestCase
{
  protected $request;
  protected $toolkit;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
    $this->request = $this->toolkit->getRequest();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testDispatch()
  {
    $config_array = array(array('path' => '/:controller/:action',
                                'defaults' => array('action' => 'display')));
    $routes = new lmbRoutes($config_array);
    $this->toolkit->setRoutes($routes);

    $this->request->getUri()->reset('/news');

    $dispatcher = new lmbRoutesRequestDispatcher();
    $result = $dispatcher->dispatch($this->request);

    $this->assertEqual($result['controller'], 'news');
    $this->assertEqual($result['action'], 'display');
  }

  function testUseActionFromRequestEvenIfMatchedByRoutes()
  {
    $config_array = array(array('path' => '/:controller/:action'));
    $routes = new lmbRoutes($config_array);
    $this->toolkit->setRoutes($routes);

    $this->request->getUri()->reset('/news/display');
    $this->request->set('action', 'admin_display'); // !!!

    $dispatcher = new lmbRoutesRequestDispatcher();
    $result = $dispatcher->dispatch($this->request);

    $this->assertEqual($result['controller'], 'news');
    $this->assertEqual($result['action'], 'admin_display');
  }

  function testUseControllerNameFromRequestEvenIfMatchedByRoutes()
  {
    $config_array = array(array('path' => '/:controller/:action'));
    $routes = new lmbRoutes($config_array);
    $this->toolkit->setRoutes($routes);

    $this->request->getUri()->reset('/news/display');
    $this->request->set('action', 'admin_display'); // !!!
    $this->request->set('controller', 'my_controller'); // !!!

    $dispatcher = new lmbRoutesRequestDispatcher();
    $result = $dispatcher->dispatch($this->request);

    $this->assertEqual($result['controller'], 'my_controller');
    $this->assertEqual($result['action'], 'admin_display');
  }

  function testNormalizeUrl()
  {
    $config_array = array(array('path' => '/:controller/:action'));
    $routes = new lmbRoutes($config_array);
    $this->toolkit->setRoutes($routes);

    $dispatcher = new lmbRoutesRequestDispatcher();

    $this->request->getUri()->reset('/news/admin_display');
    $result = $dispatcher->dispatch($this->request);
    $this->assertEqual($result['controller'], 'news');
    $this->assertEqual($result['action'], 'admin_display');

    $this->request->getUri()->reset('/blog////index');
    $result = $dispatcher->dispatch($this->request);
    $this->assertEqual($result['controller'], 'blog');
    $this->assertEqual($result['action'], 'index');

    $this->request->getUri()->reset('/blog/../bar/index/');
    $result = $dispatcher->dispatch($this->request);
    $this->assertEqual($result['controller'], 'bar');
    $this->assertEqual($result['action'], 'index');
  }

  function testDispatchWithOffset()
  {
    $config_array = array(array('path' => ':controller/:action'));
    $routes = new lmbRoutes($config_array);
    $this->toolkit->setRoutes($routes);

    $dispatcher = new lmbRoutesRequestDispatcher($path_offset = '/www',
                                                 $base_path = 'http://example.com/app/');

    $this->request->getUri()->reset('http://example.com/app/news/admin_display');
    $result = $dispatcher->dispatch($this->request);
    $this->assertEqual($result['controller'], 'news');
    $this->assertEqual($result['action'], 'admin_display');
  }
}


