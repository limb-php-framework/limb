<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
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

