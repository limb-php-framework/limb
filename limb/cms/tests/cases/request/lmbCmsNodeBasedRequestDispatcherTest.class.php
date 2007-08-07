<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/cms/src/request/lmbCmsNodeBasedRequestDispatcher.class.php');
lmb_require('limb/cms/src/model/lmCmsNode.class.php');

class lmbCmsNodeBasedRequestDispatcherTest extends UnitTestCase
{
  protected $request;
  protected $toolkit;
  protected $tree;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
    $this->request = $this->toolkit->getRequest();
    $this->tree = $this->toolkit->getCmsTree();
  }

  function tearDown()
  {
    $this->tree->deleteAll();
    lmbToolkit :: restore();
  }

  function testDispatchWithDefaultAction()
  {
    $root = $this->_createNode('root');
    $child = $this->_createNode('news', $root, 'news');

    $this->request->getUri()->reset('/root/news');

    $dispatcher = new lmbCmsNodeBasedRequestDispatcher();
    $result = $dispatcher->dispatch($this->request);

    $this->assertEqual($result['controller'], 'news');
  }

  function testDispatchWithAction()
  {
    $root = $this->_createNode('root');
    $child = $this->_createNode('news', $root, 'news');

    $this->request->getUri()->reset('/root/news');
    $this->request->set('action', 'show');

    $dispatcher = new lmbCmsNodeBasedRequestDispatcher();
    $result = $dispatcher->dispatch($this->request);

    $this->assertEqual($result['controller'], 'news');
    $this->assertEqual($result['action'], 'show');
  }

  function testDispatchToParent()
  {
    $root = $this->_createNode('root', null, 'default');
    $child = $this->_createNode('news', $root, 'news');

    $this->request->getUri()->reset('/root');

    $dispatcher = new lmbCmsNodeBasedRequestDispatcher();
    $result = $dispatcher->dispatch($this->request);

    $this->assertEqual($result['controller'], 'default');
  }

  protected function _createNode($node_identifier, $parent_node = null, $controller_name = 'default')
  {
    $node = new lmbCmsNode();
    $node->setTitle('title_'. mt_rand(0, 10000));
    $node->setIdentifier($node_identifier);
    $node->setControllerName($controller_name);
    if($parent_node)
      $node->setParent($parent_node);
    $node->save();
    return $node;
  }
}


