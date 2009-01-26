<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require(dirname(__FILE__).'/../../../../src/im/lmbImImageContainer.class.php');
lmb_require(dirname(__FILE__).'/../../../../src/im/filters/lmbImWaterMarkImageFilter.class.php');

class lmbImWaterMarkImageFilterTest extends UnitTestCase
{

  function _getInputImage()
  {
    return dirname(__FILE__).'/../../../var/input.jpg';
  }

  function _getWaterMarkImage()
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

  function testWaterMark()
  {
    $cont = $this->_getContainer();
    $filter = new lmbImWaterMarkImageFilter(array('water_mark' => $this->_getWaterMarkImage(), 'x' => 5, 'y' => 6));

    $filter->apply($cont);
    $cont->save($this->_getOutputImage());
    list($width, $height, $type) = getimagesize($this->_getInputImage());
    list($width2, $height2, $type2) = getimagesize($this->_getOutputImage());
    $this->assertEqual($width, $width2);
    $this->assertEqual($height, $height2);
    $this->assertEqual($type, $type2);
  }

  function testParams()
  {
    $filter = new lmbImWaterMarkImageFilter(array('water_mark' => 'input.jpg', 'x' => 90, 'y' => 100, 'opacity' => 20));

    $this->assertEqual($filter->getWaterMark(), 'input.jpg');
    $this->assertEqual($filter->getX(), 90);
    $this->assertEqual($filter->getY(), 100);
    $this->assertEqual($filter->getOpacity(), 20);
  }

  function testCalcPosition()
  {
    $filter = new lmbImWaterMarkImageFilter(array());

    $result = $filter->calcPosition(10, 100, 150, 250);
    $this->assertEqual($result[0], 10);
    $this->assertEqual($result[1], 100);

    $result = $filter->calcPosition(-10, 100, 150, 250);
    $this->assertEqual($result[0], 140);
    $this->assertEqual($result[1], 100);

    $result = $filter->calcPosition(10, -100, 150, 250);
    $this->assertEqual($result[0], 10);
    $this->assertEqual($result[1], 150);

    $result = $filter->calcPosition(-10, -100, 150, 250);
    $this->assertEqual($result[0], 140);
    $this->assertEqual($result[1], 150);
  }

  function tearDown()
  {
    @unlink($this->_getOutputImage());
  }
}
