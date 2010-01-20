<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/imagekit/tests/cases/lmbImageKitTestCase.class.php');

abstract class lmbBaseRotateImageFilterTest extends lmbImageKitTestCase
{
  function testRotate()
  {
    $cont = $this->_getContainer();
    $class_name = $this->_getClass('lmb%RotateImageFilter');
    $filter = new $class_name(array('angle' => 90));

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
    $class_name = $this->_getClass('lmb%RotateImageFilter');
    $filter = new $class_name(array('angle' => 90, 'bgcolor' => 'FF0000'));

    $this->assertEqual($filter->getAngle(), 90);
    $bgcolor = $filter->getBgColor();
    $this->assertEqual($bgcolor['red'], 255);
    $this->assertEqual($bgcolor['green'], 0);
    $this->assertEqual($bgcolor['blue'], 0);
  }
}
