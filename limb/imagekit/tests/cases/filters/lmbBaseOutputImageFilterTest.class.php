<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/imagekit/tests/cases/lmbImageKitTestCase.class.php');

abstract class lmbBaseOutputImageFilterTest extends lmbImageKitTestCase
{
  function testChangeOutput()
  {
    $cont = $this->_getContainer();
    $cont->setOutputType('gif');

    $class_name = 'lmb'.lmb_camel_case($this->driver).'OutputImageFilter';
    $filter = new $class_name(array('type' => 'jpeg'));
    $filter->apply($cont);

    $this->assertEqual($cont->getOutputType(), 'jpeg');
  }

}
