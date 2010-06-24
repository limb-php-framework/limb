<?php

lmb_require('limb/core/src/lmbSet.class.php');
lmb_require('limb/wysiwyg/src/lmbWysiwygConfigurationHelper.class.php');

class lmbWysiwygConfigurationHelperTest extends UnitTestCase 
{
  /**
   * @var lmbWysiwygConfigurationHelper
   */
  protected $_helper;
    
  function setUp()
  {
    parent::setUp();
   
    $config = new lmbSet(array(
      'default_profile' => 'foo_profile',
      'foo_profile' => array(
        'type' => 'fckeditor',
        'baz' => 42,
       ),
      'bar_profile' => array(
        'type' => 'tinymce',
        'baz' => 111,
      ),
    ));   
   
    lmbToolkit::instance()->setConf('wysiwyg', $config);
    
    $this->_helper = new lmbWysiwygConfigurationHelper();
  }
  
  function testSetGetProfileName()
  {    
    $this->assertEqual($this->_helper->getProfileName(), 'foo_profile');
    
    $this->_helper->setProfileName('bar_profile');
    $this->assertEqual($this->_helper->getProfileName(), 'bar_profile');
  }
  
  function testGetOption_DefaultProfile()
  {    
    $this->assertEqual($this->_helper->getOption('baz'), 42);
  }
  
  function testGetOption_CustomProfile()
  {    
    $this->_helper->setProfileName('bar_profile');
    $this->assertEqual($this->_helper->getOption('baz'), 111);
  }
  
  function testGetMacroWidgetInfo()
  {    
    $this->assertIdentical(
      $this->_helper->getMacroWidgetInfo(),
      array(
        'file' => 'limb/wysiwyg/src/macro/lmbMacroFCKEditorWidget.class.php',
        'class' => 'lmbMacroFCKEditorWidget'
      )
    );
  }
}