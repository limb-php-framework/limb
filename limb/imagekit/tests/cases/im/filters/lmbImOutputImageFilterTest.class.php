<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require(dirname(__FILE__).'/../../../../src/im/lmbImImageContainer.class.php');
lmb_require(dirname(__FILE__).'/../../../../src/im/filters/lmbImOutputImageFilter.class.php');

class lmbImOutputImageFilterTest extends UnitTestCase 
{

  function testChangeOutput()
  {
    $cont = new lmbImImageContainer();
    $cont->setOutputType('gif');

    $filter = new lmbImOutputImageFilter(array('type' => 'jpeg'));
    $filter->apply($cont);

    $this->assertEqual($cont->getOutputType(), 'jpeg');
  }

}
