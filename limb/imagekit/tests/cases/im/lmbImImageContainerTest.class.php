<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require(dirname(__FILE__).'/../../../src/im/lmbImImageContainer.class.php');

class lmbImImageContainerTest extends UnitTestCase 
{

  function _getInputImage()
  {
    return dirname(__FILE__).'/../../var/input.jpg';
  }

  function _getPalleteImage()
  {
    return dirname(__FILE__).'/../../var/water_mark.gif';
  }

  function _getOutputImage()
  {
    return dirname(__FILE__).'/../../var/output.jpg';
  }

  function _getOutputImageGif()
  {
    return dirname(__FILE__).'/../../var/output.gif';
  }

  function _getOutputImagePng()
  {
    return dirname(__FILE__).'/../../var/output.png';
  }

  function testLoadSave()
  {
  	$cont = new lmbImImageContainer();
    $cont->load($this->_getInputImage());
    $cont->save($this->_getOutputImage());

    list($width, $height, $type) = getimagesize($this->_getInputImage());
    list($width2, $height2, $type2) = getimagesize($this->_getOutputImage());
    $this->assertEqual($width, $width2);
    $this->assertEqual($height, $height2);
    $this->assertEqual($type, $type2);
  }

  function testChangeType()
  {
    $cont = new lmbImImageContainer();
    $cont->load($this->_getInputImage(), 'JPEG');
    $cont->setOutputType('PNG');
    $cont->save($this->_getOutputImagePng());
    list($width, $height, $type) = getimagesize($this->_getInputImage());
    list($width2, $height2, $type2) = getimagesize($this->_getOutputImagePng());
    $this->assertEqual($width, $width2);
    $this->assertEqual($height, $height2);
    $this->assertNotEqual($type, $type2);
    $this->assertEqual(lmbImImageContainer::convertImageType($type), "JPEG");
    $this->assertEqual(lmbImImageContainer::convertImageType($type2), "PNG");
  }

  function testGetSize()
  {
    $cont = new lmbImImageContainer();
    $cont->load($this->_getInputImage());

    $this->assertEqual($cont->getWidth(), 100);
    $this->assertEqual($cont->getHeight(), 137);
  }

  function testIsPallete()
  {
    $cont = new lmbImImageContainer();
    $cont->load($this->_getInputImage());
    $this->assertFalse($cont->isPallete());

    $cont->load($this->_getPalleteImage());
    $this->assertTrue($cont->isPallete());
  }

  function testSetGetOutputType()
  {
    $cont = new lmbImImageContainer();
    $cont->setOutputType('gif');

    $this->assertEqual($cont->getOutputType(), 'gif');
  }
  
  function testSave_ParamQuality()
  {
    $cont = new lmbImImageContainer();
    $cont->load($this->_getInputImage());
    $cont->setOutputType('jpeg');
    $cont->save($this->_getOutputImage(), 100);
    
    clearstatcache();
    $size_quality_100 = filesize($this->_getOutputImage());
    
    $cont->load($this->_getInputImage());
    $cont->setOutputType('jpeg');
    $cont->save($this->_getOutputImage(), 0);
    
    clearstatcache();
    $size_quality_0 = filesize($this->_getOutputImage());
    
    $this->assertTrue($size_quality_0 < $size_quality_100);
  }

  function tearDown()
  {
  	@unlink($this->_getOutputImage());
  	@unlink($this->_getOutputImageGif());
  	@unlink($this->_getOutputImagePng());
  }
}
