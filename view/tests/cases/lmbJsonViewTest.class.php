<?php
/*
* Limb PHP Framework
*
* @link http://limb-project.com
* @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
* @license    LGPL http://www.gnu.org/copyleft/lesser.html
*/
lmb_require('limb/view/src/lmbJsonView.class.php');

class lmbJsonViewTest extends UnitTestCase
{
  /**
   * @var lmbJsonView
   */
  protected $view;
  
  function setUp()
  {
    $this->view = new lmbJsonView();
  }
  
  protected function _checkValue($must_be)
  {
    $this->assertEqual($this->view->render(), $must_be);    
    $this->view->useEmulation(true);
    $this->assertEqual($this->view->render(), $must_be);
  }
  
  function testRender_empty()
  {    
    $this->_checkValue('[]');
  }
    
  function testRender_bool()
  {            
    $this->view->set('positive', true);
    $this->view->set('negative', false);
    $this->_checkValue('{"positive":true,"negative":false}');
  }
  
  function testRender_integer()
  {        
    $this->view->set('integer', 42);
    $this->_checkValue('{"integer":42}');    
  }
  
  function testRender_string()
  {        
    $this->view->set('string', 'foo');
    $this->_checkValue('{"string":"foo"}');
  }
  
  function testRender_array()
  {        
    $this->view->set('array', array(array(array('foo' => 42))));
    $this->_checkValue('{"array":[[{"foo":42}]]}');
  }
  
  function testRender_object()
  {        
    $object = new lmbObject();
    $object->set('foo', 42);
    
    $this->view->set('object', $object);
    $this->_checkValue('{"object":{"foo":42}}');
  }

}
