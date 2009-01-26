<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require(dirname(__FILE__).'/../../../../src/im/lmbImImageContainer.class.php');
lmb_require(dirname(__FILE__).'/../../../../src/im/filters/lmbImRotateImageFilter.class.php');

class lmbImRotateImageFilterTest extends UnitTestCase 
{

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
    $cont = new lmbImImageContainer();
    $cont->load($this->_getInputImage());
    return $cont;
  }

  function testRotate()
  {
    $cont = $this->_getContainer();
    $filter = new lmbImRotateImageFilter(array('angle' => 90));

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
    $filter = new lmbImRotateImageFilter(array('angle' => 90, 'bgcolor' => 'FF0000'));

    $this->assertEqual($filter->getAngle(), 90);
    $bgcolor = $filter->getBgColor();
    $this->assertEqual($bgcolor, 'FF0000');
  }

  function tearDown()
  {
    @unlink($this->_getOutputImage());
  }
}
