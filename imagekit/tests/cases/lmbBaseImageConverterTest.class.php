<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/imagekit/src/gd/lmbGdImageConverter.class.php');
lmb_require('limb/imagekit/tests/cases/lmbImageKitTestCase.class.php');

abstract class lmbBaseImageConverterTest extends lmbImageKitTestCase
{
  function testApply()
  {
    $conv = $this->_getConverter();
    $conv->load($this->_getInputImage());
    $conv->apply('resize', array('width' => 50, 'height' => 70, 'preserve_aspect_ratio' => false));

    $conv->save($this->_getOutputImage());
    list($width, $height, $type) = getimagesize($this->_getOutputImage());
    $this->assertEqual($width, 50);
    $this->assertEqual($height, 70);
  }

  function testApplyByOverload()
  {
    $conv = $this->_getConverter();
    $conv->load($this->_getInputImage());
    $conv->resize(array('width' => 50, 'height' => 70, 'preserve_aspect_ratio' => false));

    $conv->save($this->_getOutputImage());
    list($width, $height, $type) = getimagesize($this->_getOutputImage());
    $this->assertEqual($width, 50);
    $this->assertEqual($height, 70);
  }

  function testApplyBatch()
  {
    $batch = array(
      array('resize' => array('width' => 50, 'height' => 60, 'preserve_aspect_ratio' => false)),
      array('crop' => array('width' => 30, 'height' => 40, 'x' => 0, 'y' => 0))
    );
    $conv = $this->_getConverter();
    $conv->load($this->_getInputImage());
    $conv->applyBatch($batch);

    $conv->save($this->_getOutputImage());
    list($width, $height, $type) = getimagesize($this->_getOutputImage());
    $this->assertEqual($width, 30);
    $this->assertEqual($height, 40);
  }

  function testFilterLocator()
  {
    $path = dirname(__FILE__).'/../fixture/filters';
    $conv = $this->_getConverter(array('add_filters_scan_dirs' => $path));
    $conv->load($this->_getInputImage());
    $conv->apply('test');
    $conv = $this->_getConverter(array('add_filters_scan_dirs' => array($path)));
    $conv->load($this->_getInputImage());
    $conv->apply('test');
    $conv = $this->_getConverter(array('filters_scan_dirs' => $path));
    $conv->load($this->_getInputImage());
    $conv->apply('test');
    $conv = $this->_getConverter(array('filters_scan_dirs' => array($path)));
    $conv->load($this->_getInputImage());
    $conv->apply('test');
  }

  function testCheckSupportConv()
  {
    $conv = $this->_getConverter();

    $this->assertTrue($conv->isSupportConversion('', 'jpeg', 'gif'));
    $this->assertTrue($conv->isSupportConversion($this->_getInputImage()));
    $this->assertFalse($conv->isSupportConversion($this->_getInputImage(), '', 'zxzx'));
  }

}
