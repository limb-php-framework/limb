<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_app/src/request/lmbRoutes.class.php');
lmb_require('limb/web_app/src/controller/lmbController.class.php');

class lmbRouteUrlTagTest extends lmbWactTestCase
{
  function testAllParamsAreStaticAndUseNamedRoute()
  {
    $config = array('blog' => array('path' => '/blog/:controller/:action'),
                    'news' => array('path' => '/:controller/:action'));

    $routes = $this->_createRoutes($config);

    $template = '<route_url route="news" params="controller:news,action:archive" onclick="something"></route_url>';

    $this->registerTestingTemplate('/limb/route_url_tag_static_attributes.html', $template);

    $page = $this->initTemplate('/limb/route_url_tag_static_attributes.html');

    $url  = lmbToolkit :: instance()->getRoutesUrl(array('controller' => 'news', 'action' => 'archive'), 'news');
    $expected = '<a onclick="something" href="'. $url . '"></a>';
    $this->assertEqual($page->capture(), $expected);
  }

  function testAllParamsWithSpaces()
  {
    $config = array('blog' => array('path' => '/blog/:controller/:action'),
                    'news' => array('path' => '/:controller/:action'));

    $routes = $this->_createRoutes($config);

    $template = '<route_url route="news" params="controller:news, action: archive" onclick="something"></route_url>';

    $this->registerTestingTemplate('/limb/route_url_tag_static_attributes_spaces.html', $template);

    $page = $this->initTemplate('/limb/route_url_tag_static_attributes_spaces.html');

    $url  = lmbToolkit :: instance()->getRoutesUrl(array('controller' => 'news', 'action' => 'archive'), 'news');
    $expected = '<a onclick="something" href="'. $url . '"></a>';
    $this->assertEqual($page->capture(), $expected);
  }
  
  function testWithDynamicParams()
  {
    $config = array('blog' => array('path' => '/blog/:controller/:action'),
                    'news' => array('path' => '/:controller/:action'));

    $routes = $this->_createRoutes($config);

    $template = '<route_url route="news" params="controller:{$controller},action:{$action}" onclick="something"></route_url>';

    $this->registerTestingTemplate('/limb/route_url_tag_dynamic.html', $template);

    $page = $this->initTemplate('/limb/route_url_tag_dynamic.html');
    $page->set('controller', $controller = 'news');
    $page->set('action', $action = 'archive');

    $url  = lmbToolkit :: instance()->getRoutesUrl(array('controller' => 'news', 'action' => 'archive'), 'news');
    $expected = '<a onclick="something" href="'. $url . '"></a>';
    $this->assertEqual($page->capture(), $expected);
  }

  function testWithComplexDBEParams()
  {
    $config = array('blog' => array('path' => '/blog/:controller/:action'),
                    'news' => array('path' => '/:controller/:action'));

    $routes = $this->_createRoutes($config);

    $template = '<route_url route="news" params="controller:{$#request.controller},action:{$#request.action}" onclick="something"></route_url>';

    $this->registerTestingTemplate('/limb/route_url_tag_dynamic_complex_dbe.html', $template);

    $page = $this->initTemplate('/limb/route_url_tag_dynamic_complex_dbe.html');

    $dataspace = new lmbSet();
    $dataspace->set('controller', $controller = 'news');
    $dataspace->set('action', $action = 'archive');
    $page->set('request', $dataspace);

    $url  = lmbToolkit :: instance()->getRoutesUrl(array('controller' => 'news', 'action' => 'archive'), 'news');
    $expected = '<a onclick="something" href="'. $url . '"></a>';
    $this->assertEqual($page->capture(), $expected);
  }

  function testTryToGuessRoute()
  {
    $config = array('blog' => array('path' => '/blog/:action'),
                    'news' => array('path' => '/:controller/:action'));

    $routes = $this->_createRoutes($config);

    $template = '<route_url params="controller:news,action:archive"></route_url>';

    $this->registerTestingTemplate('/limb/route_url_tag_no_route_name.html', $template);

    $page = $this->initTemplate('/limb/route_url_tag_no_route_name.html');

    $url  = lmbToolkit :: instance()->getRoutesUrl(array('controller' => 'news', 'action' => 'archive'));
    $expected = '<a href="'. $url . '"></a>';
    $this->assertEqual($page->capture(), $expected);
  }

  function testExtraHrefChunk()
  {
    $config = array('blog' => array('path' => '/blog/:action'),
                    'news' => array('path' => '/:controller/:action'));

    $routes = $this->_createRoutes($config);

    $template = '<route_url params="controller:news,action:archive" extra="?id=50"></route_url>';

    $this->registerTestingTemplate('/limb/route_url_tag_extra_href_chunk.html', $template);

    $page = $this->initTemplate('/limb/route_url_tag_extra_href_chunk.html');

    $url  = lmbToolkit :: instance()->getRoutesUrl(array('controller' => 'news', 'action' => 'archive'));
    $expected = '<a href="'. $url . '?id=50"></a>';

    $this->assertEqual($page->capture(), $expected);
  }

  
  function testRouteWithSkipController()
  {
    $toolkit = lmbToolkit :: instance();
    $toolkit->setDispatchedController(new lmbController());

    $config = array('blog' => array('path' => '/blog/:action'));

    $routes = $this->_createRoutes($config);

    $template = '<route_url params="action:archive" skip_controller="true"/>';

    $this->registerTestingTemplate('/limb/routes_tag_route_with_skip_controller.html', $template);

    $page = $this->initTemplate('/limb/routes_tag_route_with_skip_controller.html');

    
    $url = $toolkit->getRoutesUrl(array('action' => 'archive'), null, $skip_controller = true);
    $expected = '<a href="'. $url . '" />';
    
    
    $this->assertEqual($page->capture(), $expected);
  }
  
  
  function _createRoutes($config)
  {
    $routes = new lmbRoutes($config);
    $this->toolkit->setRoutes($routes);
    return $routes;
  }
}

