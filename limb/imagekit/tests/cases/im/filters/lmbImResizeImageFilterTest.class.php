<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require(dirname(__FILE__).'/../../../../src/im/lmbImImageContainer.class.php');
lmb_require(dirname(__FILE__).'/../../../../src/im/filters/lmbImResizeImageFilter.class.php');

class lmbImResizeImageFilterTest extends UnitTestCase
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

  function testSimpleResize()
  {
    $cont = $this->_getContainer();
    $filter = new lmbImResizeImageFilter(array('width' => 50, 'height' => 70, 'preserve_aspect_ratio' => false));

    $filter->apply($cont);
    $cont->save($this->_getOutputImage());
    list($width, $height, $type) = getimagesize($this->_getOutputImage());
    $this->assertEqual($width, 50);
    $this->assertEqual($height, 70);
  }

  function testPreserveAspectRatio()
  {
    $filter = new lmbImResizeImageFilter(array());

    list($w, $h) = $filter->calcSize(100, 60, 20, 30, true);
    $this->assertEqual($w, 20);
    $this->assertEqual($h, 12);

    list($w, $h) = $filter->calcSize(60, 100, 20, 30, true);
    $this->assertEqual($w, 18);
    $this->assertEqual($h, 30);
  }

  function testSaveMinSize()
  {
    $filter = new lmbImResizeImageFilter(array());

    list($w, $h) = $filter->calcSize(100, 60, 20, 30, true, true);
    $this->assertEqual($w, 20);
    $this->assertEqual($h, 12);

    list($w, $h) = $filter->calcSize(60, 100, 20, 30, true, true);
    $this->assertEqual($w, 18);
    $this->assertEqual($h, 30);

    list($w, $h) = $filter->calcSize(10, 20, 20, 30, true, true);
    $this->assertEqual($w, 10);
    $this->assertEqual($h, 20);

    list($w, $h) = $filter->calcSize(10, 20, 20, 30, true);
    $this->assertEqual($w, 15);
    $this->assertEqual($h, 30);

    list($w, $h) = $filter->calcSize(10, 20, 20, 30, false, true);
    $this->assertEqual($w, 10);
    $this->assertEqual($h, 20);
  }

  function testParams()
  {
    $filter = new lmbImResizeImageFilter(array('width' => 90, 'height' => 100, 'preserve_aspect_ratio' => false, 'save_min_size' => true));

    $this->assertEqual($filter->getWidth(), 90);
    $this->assertEqual($filter->getHeight(), 100);
    $this->assertFalse($filter->getPreserveAspectRatio());
    $this->assertTrue($filter->getSaveMinSize());
  }

  function tearDown()
  {
    @unlink($this->_getOutputImage());
  }
}
