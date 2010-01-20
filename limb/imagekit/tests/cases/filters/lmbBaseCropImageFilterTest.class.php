<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/imagekit/tests/cases/lmbImageKitTestCase.class.php');

/**
 * @package imagekit
 * @version $Id: lmbGdCropImageFilterTest.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
abstract class lmbBaseCropImageFilterTest extends lmbImageKitTestCase
{
  function testTrueColorCrop()
  {
    $cont = $this->_getContainer();
    $class_name = $this->_getClass('lmb%CropImageFilter');
    $filter = new $class_name(array('width' => 50, 'height' => 70, 'x' => 10, 'y' => 20));

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
    $class_name = $this->_getClass('lmb%CropImageFilter');
    $filter = new $class_name(array('width' => 5, 'height' => 10));

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
    $class_name = $this->_getClass('lmb%CropImageFilter');
    $filter = new $class_name(array('width' => 100, 'height' => 100));

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
    $class_name = $this->_getClass('lmb%CropImageFilter');
    $filter = new $class_name(array('x' => 10, 'y' => 20, 'width' => 40, 'height' => 50));

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
    $class_name = $this->_getClass('lmb%CropImageFilter');
    $filter = new $class_name(array('x' => 10, 'y' => 20));

    $filter->apply($cont);
    $cont->save($this->_getOutputImage());
    list($width, $height, $type) = getimagesize($this->_getOutputImage());
    $this->assertEqual($width, 90);
    $this->assertEqual($height, 117);
    $cont->load($this->_getOutputImage());
  }

  function testParams()
  {
    $class_name = $this->_getClass('lmb%CropImageFilter');
    $filter = new $class_name(array('width' => 90, 'height' => 100, 'x' => 10, 'y' => 20));

    $this->assertEqual($filter->getWidth(), 90);
    $this->assertEqual($filter->getHeight(), 100);
    $this->assertEqual($filter->getX(), 10);
    $this->assertEqual($filter->getY(), 20);
  }
}