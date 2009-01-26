<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require(dirname(__FILE__).'/../../src/lmbImageKit.class.php');

class lmbImageKitTest extends UnitTestCase
{
  function skip()
  {
    $this->skipIf(!extension_loaded('gd'), 'GD extension not found. Test skipped.');
    $this->skipIf(!function_exists('imagerotate'), 'imagerotate() function does not exist. Test skipped.');
  }

  function _getInputImage()
  {
    return dirname(__FILE__).'/../var/input.jpg';
  }

  function _getOutputImage()
  {
    return dirname(__FILE__).'/../var/output.jpg';
  }

  function testCreateGdConvertor()
  {
    $conv = lmbImageKit::create('gd');
    $this->assertIsA($conv, 'lmbGdImageConvertor');
  }

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
    lmbImageKit::load($this->_getInputImage(), '', 'gd', '', array('add_filters_scan_dirs' => dirname(__FILE__).'/../var/filters'))
      ->test();
  }

  function tearDown()
  {
    @unlink($this->_getOutputImage());
  }
}
