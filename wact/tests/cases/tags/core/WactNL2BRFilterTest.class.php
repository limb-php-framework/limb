<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class WactNL2BRFilterTest extends WactTemplateTestCase
{
  function testFilter()
  {
    $template = '{$test|nl2br|raw}';
    $this->registerTestingTemplate('/filters/core/text/var.html', $template);

    $page = $this->initTemplate('/filters/core/text/var.html');
    $page->set('test', "Hello\nSailor!");
    $output = $page->capture();
    $this->assertEqual($output, "Hello<br />\nSailor!");
  }
}

