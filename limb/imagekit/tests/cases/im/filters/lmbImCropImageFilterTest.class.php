<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/imagekit/src/im/lmbImImageContainer.class.php');
lmb_require('limb/imagekit/src/im/filters/lmbImCropImageFilter.class.php');

/**
 * @package imagekit
 * @version $Id: $
 */
class lmbImCropImageFilterTest extends UnitTestCase
{

  function _getInputImage()
  {
    return dirname(__FILE__).'/../../../var/input.jpg';
  }

  function _getInputPalleteImage()
  {
    return dirname(__FILE__).'/../../../var/water_mark.gif';
  }

  function _getOutputImage()
  {
    return dirname(__FILE__).'/../../../var/output.jpg';
  }

  function _getContainer()
  {
    $cont = new lmbImImageContainer();
    $cont->load($this->_getInputImage());
    return $cont;
  }

  function _getPalleteContainer()
  {
    $cont = new lmbImImageContainer();
    $cont->load($this->_getInputPalleteImage());
    return $cont;
  }

  function testTrueColorCrop()
  {
    $cont = $this->_getContainer();
    $filter = new lmbImCropImageFilter(array('width' => 50, 'height' => 70, 'x' => 10, 'y' => 20));

    $filter->apply($cont);
    $cont->save($this->_getOutputImage());
    list($width, $height, $type) = getimagesize($this->_getOutputImage());
    $this->assertEqual($width, 50);
    $this->assertEqual($height, 70);
    $cont->load($this->_getOutputImage());
    $this->assertFalse($cont->isPallete());
  }

  function testPalleteCrop()
  {
    $cont = $this->_getPalleteContainer();
    $filter = new lmbImCropImageFilter(array('width' => 5, 'height' => 10));

    $filter->apply($cont);
    $cont->save($this->_getOutputImage());
    list($width, $height, $type) = getimagesize($this->_getOutputImage());
    $this->assertEqual($width, 5);
    $this->assertEqual($height, 10);
    $cont->load($this->_getOutputImage());
    $this->assertTrue($cont->isPallete());
  }
  
  function testMaxSizeRestriction()
  {
    $cont = $this->_getPalleteContainer();
    $filter = new lmbImCropImageFilter(array('width' => 100, 'height' => 100));

    $filter->apply($cont);
    $cont->save($this->_getOutputImage());
    list($width, $height, $type) = getimagesize($this->_getOutputImage());
    $this->assertEqual($width, 14);
    $this->assertEqual($height, 15);
    $cont->load($this->_getOutputImage());
  }

  function testCropOfInternalArea()
  {
    $cont = $this->_getContainer();
    $filter = new lmbImCropImageFilter(array('x' => 10, 'y' => 20, 'width' => 40, 'height' => 50));

    $filter->apply($cont);
    $cont->save($this->_getOutputImage());
    list($width, $height, $type) = getimagesize($this->_getOutputImage());
    $this->assertEqual($width, 40);
    $this->assertEqual($height, 50);
    $cont->load($this->_getOutputImage());
  }

  function testAutoDetectionOfSize()
  {
    $cont = $this->_getContainer();
    $filter = new lmbImCropImageFilter(array('x' => 10, 'y' => 20));

    $filter->apply($cont);
    $cont->save($this->_getOutputImage());
    list($width, $height, $type) = getimagesize($this->_getOutputImage());
    $this->assertEqual($width, 90);
    $this->assertEqual($height, 117);
    $cont->load($this->_getOutputImage());
  }

  function testParams()
  {
    $filter = new lmbImCropImageFilter(array('width' => 90, 'height' => 100, 'x' => 10, 'y' => 20));

    $this->assertEqual($filter->getWidth(), 90);
    $this->assertEqual($filter->getHeight(), 100);
    $this->assertEqual($filter->getX(), 10);
    $this->assertEqual($filter->getY(), 20);
  }

  function tearDown()
  {
    @unlink($this->_getOutputImage());
  }
}
