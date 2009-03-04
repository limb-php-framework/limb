<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class WactUppercaseFilterTest extends WactTemplateTestCase
{
  function testUppercaseVariable()
  {
    $template = '{$test|uppercase}';
    $this->registerTestingTemplate('/filters/core/uppercase/var.html', $template);

    $page = $this->initTemplate('/filters/core/uppercase/var.html');
    $page->set('test', 'testing');
    $output = $page->capture();
    $this->assertEqual($output, 'TESTING');
  }

  function testUpperCaseSet()
  {
    $template = '<core:SET test="testing"/>{$test|uppercase}';
    $this->registerTestingTemplate('/filters/core/uppercase/set.html', $template);

    $page = $this->initTemplate('/filters/core/uppercase/set.html');
    $output = $page->capture();
    $this->assertEqual($output, 'TESTING');
  }
}

