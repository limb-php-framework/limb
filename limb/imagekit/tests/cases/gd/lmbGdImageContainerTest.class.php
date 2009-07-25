<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require(dirname(__FILE__).'/../../../src/gd/lmbGdImageContainer.class.php');

class lmbGdImageContainerTest extends UnitTestCase {

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

  function testLoadSave()
  {
  	$cont = new lmbGdImageContainer();
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
    $cont = new lmbGdImageContainer();
    $cont->load($this->_getInputImage(), 'jpeg');
    $cont->setOutputType('gif');
    $cont->save($this->_getOutputImage());
    list($width, $height, $type) = getimagesize($this->_getInputImage());
    list($width2, $height2, $type2) = getimagesize($this->_getOutputImage());
    $this->assertEqual($width, $width2);
    $this->assertEqual($height, $height2);
    $this->assertNotEqual($type, $type2);
    $this->assertEqual($type, IMAGETYPE_JPEG);
    $this->assertEqual($type2, IMAGETYPE_GIF);
  }

  function testGetSize()
  {
    $cont = new lmbGdImageContainer();
    $cont->load($this->_getInputImage());

    $this->assertEqual($cont->getWidth(), 100);
    $this->assertEqual($cont->getHeight(), 137);
  }

  function testIsPallete()
  {
    $cont = new lmbGdImageContainer();
    $cont->load($this->_getInputImage());
    $this->assertFalse($cont->isPallete());

    $cont->load($this->_getPalleteImage());
    $this->assertTrue($cont->isPallete());
  }

  function testSetGetOutputType()
  {
    $cont = new lmbGdImageContainer();
    $cont->setOutputType('gif');

    $this->assertEqual($cont->getOutputType(), 'gif');
  }
  
  function testSave_ParamQuality()
  {
    $cont = new lmbGdImageContainer();
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
  
  function testCreatePalleteBlankImage()
  {
    $cont = new lmbGdImageContainer();
    $cont->load($this->_getPalleteImage());
    
    $img = $cont->createBlankImage(100, 200);
    $cont->replaceResource($img);
    
    $this->assertEqual($cont->getWidth(), 100);
    $this->assertEqual($cont->getHeight(), 200);
    $this->assertTrue($cont->isPallete());
  }

  function testCreateTruecolorBlankImage()
  {
    $cont = new lmbGdImageContainer();
    $cont->load($this->_getInputImage());
    
    $img = $cont->createBlankImage(100, 200);
    $cont->replaceResource($img);
    
    $this->assertEqual($cont->getWidth(), 100);
    $this->assertEqual($cont->getHeight(), 200);
    $this->assertFalse($cont->isPallete());
  }

  function testCreateForcedTruecolorBlankImage()
  {
    $cont = new lmbGdImageContainer();
    $cont->load($this->_getPalleteImage());
    
    $img = $cont->createBlankImage(100, 200, true);
    $cont->replaceResource($img);
    
    $this->assertEqual($cont->getWidth(), 100);
    $this->assertEqual($cont->getHeight(), 200);
    $this->assertFalse($cont->isPallete());
  }
  
  function testToTrueColor()
  {
    $cont = new lmbGdImageContainer();
    $cont->load($this->_getPalleteImage());
    
    $width = $cont->getWidth();
    $height = $cont->getHeight();
    $cont->toTrueColor();

    $this->assertEqual($cont->getWidth(), $width);
    $this->assertEqual($cont->getHeight(), $height);
    $this->assertFalse($cont->isPallete());
  }

  function tearDown()
  {
  	@unlink($this->_getOutputImage());
  }
}
