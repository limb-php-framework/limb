<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactHtmlFilterTest.class.php 5189 2007-03-06 08:06:16Z serega $
 * @package    wact
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
?>