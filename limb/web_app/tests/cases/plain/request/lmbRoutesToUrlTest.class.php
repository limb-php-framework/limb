<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbRoutesToUrlTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/request/lmbRoutes.class.php');
lmb_require('limb/net/src/lmbUri.class.php');

class lmbRoutesToUrlTest extends UnitTestCase
{
  function setUp()
  {
    $toolkit = lmbToolkit :: save();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testToUrl()
  {
    $config = array('blog' => array('path' => '/blog',
                          'defaults' => array('controller' => 'Blog',
                                              'action' => 'display')),
                    'news' => array('path' => '/news',
                          'defaults' => array('controller' => 'Newsline',
                                              'action' => 'display')));

    $routes = new lmbRoutes($config);
    $this->assertEqual($routes->toUrl(array(), 'blog'), '/blog');
    $this->assertEqual($routes->toUrl(array(), 'news'), '/news');
  }

  function testToUrlUseNamedParam()
  {
    $config = array('default' => array('path' => '/:controller/display',
                          'defaults' => array('action' => 'display')));

    $routes = new lmbRoutes($config);
    $this->assertEqual($routes->toUrl(array('controller' => 'news'), 'default'), '/news/display');
  }

  function testToUrlApplyDefaultParamValue()
  {
    $config = array('default' => array('path' => '/:controller/:action',
                          'defaults' => array('action' => 'display')));

    $routes = new lmbRoutes($config);
    $this->assertEqual($routes->toUrl(array('controller' => 'news'), 'default'), '/news/display');
  }

  function testThrowExceptionIfNotEnoughParams()
  {
    $config = array('default' => array('path' => '/:controller/:action'));

    $routes = new lmbRoutes($config);
    try
    {
      $routes->toUrl(array('controller' => 'news'), 'default');
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testThrowExceptionIfNotFoundAnyMatchingRoute()
  {
    $config = array('default' => array('path' => '/:controller/:action',
                          'defaults' => array('action' => 'display')));

    $routes = new lmbRoutes($config);
    try
    {
      $routes->toUrl(array(), 'default');
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testToUrlTryToGuessRoute()
  {
    $config = array('default' => array('path' => '/:controller/display',
                          'defaults' => array('action' => 'display')),
                     'full' => array('path' => '/:controller/:action',
                          'defaults' => array('action' => 'display')));

    $routes = new lmbRoutes($config);
    $this->assertEqual($routes->toUrl(array('controller' => 'news',
                                            'action' => 'archive')), '/news/archive');
  }

  function testNoSuchRoute()
  {
    $config = array(
      'AdminPanel' =>
        array('path' => '/admin',
              'defaults' => array('controller' => 'AdminPanel',
                                  'action' => 'admin_display')),

      'EdPrograms' =>
        array('path' => '/admin/programs/:action',
              'defaults' => array('controller' => 'EdPrograms',
                                  'action' => 'admin_display')),

      'EdCourses' =>
        array('path' => '/admin/courses/:action',
              'defaults' => array('controller' => 'EdCourses',
                                  'action' => 'admin_display')),

    );

    $routes = new lmbRoutes($config);
    try
    {
      $routes->toUrl(array('action' => 'create'), 'Course');
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testApplyUrlFilter()
  {
    $config = array('default' => array('path' => '/:controller/:action',
                                       'defaults' => array('action' => 'display'),
                                       'url_filter' => array($this, '_processUrlResult')));

    $routes = new lmbRoutes($config);
    $this->assertEqual($routes->toUrl(array('controller' => 'admin_news',
                                            'action' => 'archive')), '/admin/news/archive');
  }

  function _processUrlResult(&$path, $route)
  {
    $path = str_replace('/admin_', '/admin/', $path);
  }
}
?>
