<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbRouteUrlSetTagTest.class.php 5628 2007-04-11 12:09:20Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/request/lmbRoutes.class.php');

class lmbRouteUrlSetTagTest extends lmbWactTestCase
{
  function testPutUrlToCurrentDataspaceAllParamsAreStaticAndUseNamedRoute()
  {
    $config = array('blog' => array('path' => '/blog/:controller/:action'),
                    'news' => array('path' => '/:controller/:action'));

    $routes = $this->_createRoutes($config);

    $template = '<route_url_set field="url" route="news" params="controller:news,action:archive"/>' .
                '{$url}';

    $this->registerTestingTemplate('/limb/routes_tag_static.html', $template);

    $page = $this->initTemplate('/limb/routes_tag_static.html');

    $expected = lmbToolkit :: instance()->getRoutesUrl(array('controller' => 'news', 'action' => 'archive'), 'news');
    $this->assertEqual($page->capture(), $expected);
  }

  function testWithDynamicParams()
  {
    $config = array('blog' => array('path' => '/blog/:controller/:action'),
                    'news' => array('path' => '/:controller/:action'));

    $routes = $this->_createRoutes($config);

    $template = '<route_url_set field="url" route="news" params="controller:{$controller},action:{$action}"/>' .
                '{$url}';

    $this->registerTestingTemplate('/limb/routes_tag_dynamic.html', $template);

    $page = $this->initTemplate('/limb/routes_tag_dynamic.html');
    $page->set('controller', $controller = 'news');
    $page->set('action', $action = 'archive');

    $expected = lmbToolkit :: instance()->getRoutesUrl(array('controller' => $controller, 'action' => $action), 'news');
    $this->assertEqual($page->capture(), $expected);
  }

  function testWithComplexDBEParams()
  {
    $config = array('blog' => array('path' => '/blog/:controller/:action'),
                    'news' => array('path' => '/:controller/:action'));

    $routes = $this->_createRoutes($config);

    $template = '<route_url_set field="url" route="news" params="controller:{$#request.controller},action:{$#request.action}"/>' .
                '{$url}';

    $this->registerTestingTemplate('/limb/routes_tag_dynamic_proper_dbe.html', $template);

    $page = $this->initTemplate('/limb/routes_tag_dynamic_proper_dbe.html');

    $dataspace = new lmbSet();
    $dataspace->set('controller', $controller = 'news');
    $dataspace->set('action', $action = 'archive');
    $page->set('request', $dataspace);

    $expected = lmbToolkit :: instance()->getRoutesUrl(array('controller' => $controller, 'action' => $action), 'news');
    $this->assertEqual($page->capture(), $expected);
  }

  function testTryToGuessRoute()
  {
    $config = array('blog' => array('path' => '/blog/:action'),
                    'news' => array('path' => '/:controller/:action'));

    $routes = $this->_createRoutes($config);

    $template = '<route_url_set field="url" params="controller:news,action:archive"/>' .
                '{$url}';

    $this->registerTestingTemplate('/limb/routes_tag_no_route_name.html', $template);

    $page = $this->initTemplate('/limb/routes_tag_no_route_name.html');

    $expected = lmbToolkit :: instance()->getRoutesUrl(array('controller' => 'news', 'action' => 'archive'));
    $this->assertEqual($page->capture(), $expected);
  }

  function _createRoutes($config)
  {
    $routes = new lmbRoutes($config);
    $this->toolkit->setRoutes($routes);
    return $routes;
  }
}
?>
