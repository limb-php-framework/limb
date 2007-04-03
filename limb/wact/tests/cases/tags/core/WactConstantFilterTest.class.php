<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactConstantFilterTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

define('SOME_TESTING_FILTER_CONSTANT', 'value');

class WactConstantFilterTest extends WactTemplateTestCase
{
  function testSimple()
  {
    $template = '{$"SOME_TESTING_FILTER_CONSTANT"|const}';

    $this->registerTestingTemplate('/core/constant_filter/simple.html', $template);

    $page = $this->initTemplate('/core/constant_filter/simple.html');

    $this->assertEqual($page->capture(), 'value');
  }

  function testDBE()
  {
    $template = '{$var|const}';

    $this->registerTestingTemplate('/core/constant_filter/simpledbe.html', $template);

    $page = $this->initTemplate('/core/constant_filter/simpledbe.html');
    $page->set('var', 'SOME_TESTING_FILTER_CONSTANT');

    $this->assertEqual($page->capture(), 'value');
  }
}
?>
