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

class lmbMacroRouteUrlTagTest extends lmbMacroTestCase
{
  function testPutUrlToCurrentDataspaceAllParamsAreStaticAndUseNamedRoute()
  {
    $config = array('blog' => array('path' => '/blog/:controller/:action'),
                    'news' => array('path' => '/:controller/:action'));

    $routes = $this->_createRoutes($config);

    $template = '<a href="{{route_url field=\'url\' route=\'news\' params=\'controller:news,action:archive\'}}">Link</a>';

    $page = $this->_createMacroTemplate($template, 'tpl.html'); 
    
    $expected = '<a href="/news/archive">Link</a>';
    $this->assertEqual($page->render(), $expected); 
  }
  
  function testPutUrlwithSpaces()
  {
    $config = array('blog' => array('path' => '/blog/:controller/:action'),
                    'news' => array('path' => '/:controller/:action'));

    $routes = $this->_createRoutes($config);

    $template = '<a href="{{route_url field=\'url\' route=\'news\' params=\'controller: news , action: archive\'}}">Link</a>';

    $page = $this->_createMacroTemplate($template, 'tpl.html'); 
    
    $expected = '<a href="/news/archive">Link</a>';
    $this->assertEqual($page->render(), $expected); 
  }
  
  function testWithDynamicParams()
  {
    $config = array('blog' => array('path' => '/blog/:controller/:action'),
                    'news' => array('path' => '/:controller/:action'));

    $routes = $this->_createRoutes($config);

    $template = '<a href="{{route_url field=\'url\' route=\'news\' params=\'controller:{$#controller} , action:{$#action}\'}}">Link</a>';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $page->set('controller', $controller = 'news');
    $page->set('action', $action = 'archive');

    $expected = '<a href="/news/archive">Link</a>';
    $this->assertEqual($page->render(), $expected); 
  }

  function testTryToGuessRoute()
  {
    $config = array('blog' => array('path' => '/blog/:action'),
                    'news' => array('path' => '/:controller/:action'));

    $routes = $this->_createRoutes($config);

    $template = '<a href="{{route_url field=\'url\' params=\'controller:news,action:archive\'}}">Link</a>';                
                
    $page = $this->_createMacroTemplate($template, 'tpl.html'); 
    
    $expected = '<a href="/news/archive">Link</a>';
    $this->assertEqual($page->render(), $expected); 
  }

  function testRouteWithSkipController()
  {
    $this->toolkit->setDispatchedController(new lmbController());

    $config = array('blog' => array('path' => '/blog/:action'));

    $routes = $this->_createRoutes($config);

    $template = '<a href="{{route_url field=\'url\' params=\'action:archive\' skip_controller="true"}}">Link</a>';                

    $page = $this->_createMacroTemplate($template, 'tpl.html'); 
    
    $expected = '<a href="/blog/archive">Link</a>';
    $this->assertEqual($page->render(), $expected); 
  }

  function _createRoutes($config)
  {
    $routes = new lmbRoutes($config);
    $this->toolkit->setRoutes($routes);
    return $routes;
  }
}

