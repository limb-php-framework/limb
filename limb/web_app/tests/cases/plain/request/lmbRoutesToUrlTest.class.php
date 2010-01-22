<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
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

  function testToUrlWithPrefix()
  {
    $config = array('default' => array('path' => '/:controller/:action/:id:.html',
                          'defaults' => array('action' => 'display')));

    $routes = new lmbRoutes($config);
    $this->assertEqual($routes->toUrl(array('controller' => 'news', 'action' => 'display', 'id' => 'test')), '/news/display/test.html');
  }

  function testToUrlApplyDefaultParamValue()
  {
    $config = array('default' => array('path' => '/:controller/:action',
                          'defaults' => array('action' => 'display')));

    $routes = new lmbRoutes($config);
    $this->assertEqual($routes->toUrl(array('controller' => 'news'), 'default'), '/news/');
  }

  function testToUrlApplyDefaultParamValueWithNoParamsInPath()
  {
    $config = array('default' => array('path' => '/news/:action/:id',
                          'defaults' => array('controller' => 'news', 'action' => 'display')));

    $routes = new lmbRoutes($config);
    $this->assertEqual($routes->toUrl(array('controller' => 'news', 'action' => 'display', 'id' => 'test')), '/news/display/test');
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
  
  function testRemoveUnneededDefaultParamsFromUrl()
  {
    $config = array(
      'default' => array(
        'path' => '/users/:user/:controller/:action/:id/',
        'defaults' => array(
          'user' => 'admin',
          'controller' => 'blog',
          'action' => 'display',
          'id' => 0
        )
      )
    );
    
    $routes = new lmbRoutes($config);
    
    $this->assertEqual($routes->toUrl(array()), '/users/');
    $this->assertEqual($routes->toUrl(array('user' => 'bob')), '/users/bob/');
    $this->assertEqual($routes->toUrl(array('user' => 'admin')), '/users/');
    $this->assertEqual($routes->toUrl(array('user' => 'bob', 'action' => 'index')), '/users/bob/blog/index/');
    $this->assertEqual($routes->toUrl(array('controller' => 'article')), '/users/admin/article/');
    $this->assertEqual($routes->toUrl(array('controller' => 'article', 'id' => 5)), '/users/admin/article/display/5/');
    $this->assertEqual($routes->toUrl(array('user' => 'admin', 'action' => 'display', 'id' => 0)), '/users/');
    $this->assertEqual($routes->toUrl(array('user' => 'admin', 'id' => 19)), '/users/admin/blog/display/19/');    
  }
  
  function testToUrlChecksRequirements()
  {
    $config = array(
      'default' => array(
        'path' => '/:controller/:action/',
        'requirements' => array(
          'controller' => '/^blog$/',
          'action' => '/^[a-z]+$/'        
        )
      )    
    );
    
    $routes = new lmbRoutes($config);
        
    $this->assertEqual($routes->toUrl(array('controller' => 'blog', 'action' => 'edit')), '/blog/edit/');
    
    try 
    {
      $routes->toUrl(array('controller' => 'admin', 'action' => '123edit'));
      $routes->toUrl(array('controller' => 'zzz', 'action' => 'edit'));
      $routes->toUrl(array('controller' => 'blog', 'action' => '@#%'));
      $this->fail("Some routes do NOT match required params!");
    } catch (lmbException $e) 
    {      
      $this->assertPattern('/route .* not found .*/i', $e->getMessage());
    }    
  }  
}

