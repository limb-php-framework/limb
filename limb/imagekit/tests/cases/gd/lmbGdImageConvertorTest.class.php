<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require(dirname(__FILE__).'/../../../src/gd/lmbGdImageConvertor.class.php');

class lmbGdImageConvertorTest extends UnitTestCase {

  function _getInputImage()
  {
    return dirname(__FILE__).'/../../var/input.jpg';
  }

  function _getOutputImage()
  {
    return dirname(__FILE__).'/../../var/output.jpg';
  }

  function _getConvertor()
  {
    return new lmbGdImageConvertor();
  }

  function testSimpleResize()
  {
    $conv = $this->_getConvertor();
    $conv->addFilter('resize', array('width' => 50, 'height' => 70, 'preserve_aspect_ratio' => false));

    $conv->run($this->_getInputImage(), $this->_getOutputImage());
    list($width, $height, $type) = getimagesize($this->_getOutputImage());
    $this->assertEqual($width, 50);
    $this->assertEqual($height, 70);
  }

  function testCheckSupportConv()
  {
    $conv = $this->_getConvertor();

    $this->assertTrue($conv->isSupportConversion('', 'jpeg', 'gif'));
    $this->assertTrue($conv->isSupportConversion($this->_getInputImage()));
    $this->assertFalse($conv->isSupportConversion($this->_getInputImage(), '', 'zxzx'));
  }

  function tearDown()
  {
    @unlink($this->_getOutputImage());
  }
}
?>