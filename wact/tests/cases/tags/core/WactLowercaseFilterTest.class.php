<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class WactLowercaseFilterTest extends WactTemplateTestCase
{
  function testLowercaseVariable()
  {
    $template = '{$test|lowercase}';
    $this->registerTestingTemplate('/filters/core/lowercase/var.html', $template);

    $page = $this->initTemplate('/filters/core/lowercase/var.html');
    $page->set('test', 'TesTing');
    $output = $page->capture();
    $this->assertEqual($output, 'testing');
  }

  function testLowerCaseSet()
  {
    $template = '<core:SET test="TesTing"/>{$test|lowercase}';
    $this->registerTestingTemplate('/filters/core/lowercase/set.html', $template);

    $page = $this->initTemplate('/filters/core/lowercase/set.html');
    $output = $page->capture();
    $this->assertEqual($output, 'testing');
  }
}

