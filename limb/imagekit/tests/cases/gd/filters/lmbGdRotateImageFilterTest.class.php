<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require(dirname(__FILE__).'/../../../../src/gd/lmbGdImageContainer.class.php');
lmb_require(dirname(__FILE__).'/../../../../src/gd/filters/lmbGdRotateImageFilter.class.php');

class lmbGdRotateImageFilterTest extends UnitTestCase {

  function skip()
  {
    $this->skipIf(!function_exists('imagerotate'), 'imagerotate() function does not exist. Test skipped.');
  }

  function _getInputImage()
  {
    return dirname(__FILE__).'/../../../var/input.jpg';
  }

  function _getOutputImage()
  {
    return dirname(__FILE__).'/../../../var/output.jpg';
  }

  function _getContainer()
  {
    $cont = new lmbGdImageContainer();
    $cont->load($this->_getInputImage());
    return $cont;
  }

  function testRotate()
  {
    $cont = $this->_getContainer();
    $filter = new lmbGdRotateImageFilter(array('angle' => 90));

    $filter->apply($cont);
    $cont->save($this->_getOutputImage());
    list($width, $height, $type) = getimagesize($this->_getInputImage());
    list($width2, $height2, $type2) = getimagesize($this->_getOutputImage());
    $this->assertEqual($width, $height2);
    $this->assertEqual($height, $width2);
    $this->assertEqual($type, $type2);
  }

  function testParams()
  {
    $filter = new lmbGdRotateImageFilter(array('angle' => 90, 'bgcolor' => 'FF0000'));

    $this->assertEqual($filter->getAngle(), 90);
    $bgcolor = $filter->getBgColor();
    $this->assertEqual($bgcolor['red'], 255);
    $this->assertEqual($bgcolor['green'], 0);
    $this->assertEqual($bgcolor['blue'], 0);
  }

  function tearDown()
  {
    @unlink($this->_getOutputImage());
  }
}
