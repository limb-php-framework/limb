<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbWebAppToolsTest.class.php 5645 2007-04-12 07:13:10Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/request/lmbRoutes.class.php');
lmb_require('limb/toolkit/src/lmbMockToolsWrapper.class.php');
lmb_require('limb/core/src/lmbSet.class.php');

class lmbWebAppToolsTest extends UnitTestCase
{
  function setUp()
  {
    lmbToolkit :: save();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testToRouteUrl()
  {
    $routes_dataspace = new lmbSet();
    $config_array = array(array('path' => '/:controller/:action',
                                'defaults' => array('action' => 'display')));
    $routes = new lmbRoutes($config_array);

    $toolkit = lmbToolkit :: merge(new lmbWebAppTools());
    $toolkit->setRoutes($routes);

    $to_url_params = array('controller' => 'news', 'action' => 'archive');
    $this->assertEqual($toolkit->getRoutesUrl($to_url_params), LIMB_HTTP_GATEWAY_PATH . ltrim($routes->toUrl($to_url_params), '/'));
  }
}

?>
