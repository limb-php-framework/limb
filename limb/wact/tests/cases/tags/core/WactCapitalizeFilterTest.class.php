<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactCapitalizeFilterTest.class.php 5188 2007-03-06 07:42:21Z serega $
 * @package    wact
 */

class WactCapitalizeFilterTest extends WactTemplateTestCase
{
  function testCapitalizeVariable()
  {
    $template = '{$test|capitalize}';
    $this->registerTestingTemplate('/filters/core/capitalize/var.html', $template);

    $page = $this->initTemplate('/filters/core/capitalize/var.html');
    $page->set('test', 'testing');
    $output = $page->capture();
    $this->assertEqual($output, 'Testing');
  }

  function testCapitalizeSet()
  {
    $template = '<core:SET test="testing"/>{$test|capitalize}';
    $this->registerTestingTemplate('/filters/core/capitalize/set.html', $template);

    $page = $this->initTemplate('/filters/core/capitalize/set.html');
    $output = $page->capture();
    $this->assertEqual($output, 'Testing');
  }
}
?>