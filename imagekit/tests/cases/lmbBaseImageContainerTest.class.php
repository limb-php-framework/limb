<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/imagekit/src/gd/lmbGdImageContainer.class.php');
lmb_require('limb/imagekit/tests/cases/lmbImageKitTestCase.class.php');

abstract class lmbBaseImageContainerTest extends lmbImageKitTestCase
{
  function testLoadSave()
  {
    $cont = $this->_getContainer();
    $cont->save($this->_getOutputImage());

    list($width, $height, $type) = getimagesize($this->_getInputImage());
    list($width2, $height2, $type2) = getimagesize($this->_getOutputImage());
    $this->assertEqual($width, $width2);
    $this->assertEqual($height, $height2);
    $this->assertEqual($type, $type2);
  }

  function testChangeType()
  {
    $cont = $this->_getContainer();
    $cont->setOutputType('gif');
    $cont->save($this->_getOutputImage('gif'));

    list($width, $height, $type) = getimagesize($this->_getInputImage());
    list($width2, $height2, $type2) = getimagesize($this->_getOutputImage('gif'));

    $this->assertEqual($width, $width2);
    $this->assertEqual($height, $height2);
    $this->assertNotEqual($type, $type2);
    $this->assertEqual($type, IMAGETYPE_JPEG);
    $this->assertEqual($type2, IMAGETYPE_GIF);
  }

  function testGetSize()
  {
    $cont = $this->_getContainer();
    $this->assertEqual($cont->getWidth(), 100);
    $this->assertEqual($cont->getHeight(), 137);
  }

  function testIsPallete()
  {
    $cont = $this->_getContainer();
    $this->assertFalse($cont->isPallete());

    $cont->load($this->_getInputPalleteImage());
    $this->assertTrue($cont->isPallete());
  }

  function testSetGetOutputType()
  {
    $cont = $this->_getContainer();
    $cont->setOutputType('gif');

    $this->assertEqual($cont->getOutputType(), 'gif');
  }

  function testSave_ParamQuality()
  {
    $cont = $this->_getContainer();
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
}
