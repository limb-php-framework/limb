<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require(dirname(__FILE__).'/../../../../src/gd/lmbGdImageContainer.class.php');
lmb_require(dirname(__FILE__).'/../../../../src/gd/filters/lmbGdOutputImageFilter.class.php');
class lmbGdOutputImageFilterTest extends UnitTestCase {

  function testChangeOutput()
  {
    $cont = new lmbGdImageContainer();
    $cont->setOutputType('gif');

    $filter = new lmbGdOutputImageFilter(array('type' => 'jpeg'));
    $filter->apply($cont);

    $this->assertEqual($cont->getOutputType(), 'jpeg');
  }

}
