<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cms/src/request/lmbCmsDocumentRequestDispatcher.class.php');
lmb_require('limb/web_app/src/request/lmbRoutes.class.php');

class lmbCmsDocumentRequestDispatcherTest extends lmbCmsTestCase
{

  function _createDispatcher()
  {
    $toolkit = lmbToolkit::instance();

    $config_array = array(array('path' => '/:controller/:action',
                                'defaults' => array('action' => 'display')));
    $routes = new lmbRoutes($config_array);

    $toolkit->setRoutes($routes);

    $toolkit->getRequest()->getUri()->reset('/news');

    $dispatcher = new lmbCmsDocumentRequestDispatcher(
      $table = 'object_with_uri',
      $table_column = 'uri',
      $controller = 'text',
      $action = 'item'
    );

    $result = $dispatcher->dispatch($toolkit->getRequest());

    return $result;
  }

  function testDispatch_NotFoundInDb()
  {
    $this->assertNull($this->_createDispatcher());
  }

  function testDispatch_FoundInDb()
  {
  	$document = $this->_createDocument('news', lmbCmsDocument::findRoot());
    $document->setIsPublished(1);
    $document->save();

    $result = $this->_createDispatcher();

    $this->assertEqual($result['controller'], 'document');
    $this->assertEqual($result['action'], 'item');
    $this->assertEqual($result['id'], $document->getId());
  }
}


