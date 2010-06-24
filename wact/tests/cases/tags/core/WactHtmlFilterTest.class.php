<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class WactHtmlFilterTest extends WactTemplateTestCase
{
  function testHtmlVariable()
  {
    $template = '{$test|html|raw}';
    $this->registerTestingTemplate('/filters/core/html/var.html', $template);

    $page = $this->initTemplate('/filters/core/html/var.html');
    $page->set('test', '<hello>');
    $output = $page->capture();
    $this->assertEqual($output, '&lt;hello&gt;');
  }

  function testHtmlSet()
  {
    $template = '<core:SET test="<hello>"/>{$test|html|raw}';
    $this->registerTestingTemplate('/filters/core/html/set.html', $template);

    $page = $this->initTemplate('/filters/core/html/set.html');
    $output = $page->capture();
    $this->assertEqual($output, '&lt;hello&gt;');
  }
}

