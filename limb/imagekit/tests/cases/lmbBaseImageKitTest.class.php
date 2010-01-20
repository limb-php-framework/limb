<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/imagekit/src/lmbImageKit.class.php');
lmb_require('limb/imagekit/tests/cases/lmbImageKitTestCase.class.php');

abstract class lmbBaseImageKitTest extends lmbImageKitTestCase
{
  function testTraversing()
  {
    lmbImageKit::load($this->_getInputImage())->
      apply('resize', array('width' => 50, 'height' => 60, 'preserve_aspect_ratio' => false))->
      apply('rotate', array('angle' => 90))->
      save($this->_getOutputImage());

    list($width, $height, $type) = getimagesize($this->_getOutputImage());
    $this->assertEqual($width, 60);
    $this->assertEqual($height, 50);
  }

  function testTraversingByOverloading()
  {
    lmbImageKit::load($this->_getInputImage())->
      resize(array('width' => 50, 'height' => 60, 'preserve_aspect_ratio' => false))->
      rotate(array('angle' => 90))->
      save($this->_getOutputImage());

    list($width, $height, $type) = getimagesize($this->_getOutputImage());
    $this->assertEqual($width, 60);
    $this->assertEqual($height, 50);
  }

  function testPassingParamsToConvertor()
  {
    lmbImageKit::load($this->_getInputImage(),
                      '',
                      $this->driver,
                      '',
                      array('add_filters_scan_dirs' => dirname(__FILE__).'/../fixture/filters')
    )->test();
  }
}
