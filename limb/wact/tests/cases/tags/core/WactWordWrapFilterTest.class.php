<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactWordWrapFilterTest.class.php 5189 2007-03-06 08:06:16Z serega $
 * @package    wact
 */

class WactWordWrapFilterTest extends WactTemplateTestCase
{
  function testWordWrapVariable()
  {
    $template = '{$test|wordwrap:10}';
    $this->registerTestingTemplate('/filters/core/wordwrap/var.html', $template);

    $page = $this->initTemplate('/filters/core/wordwrap/var.html');
    $page->set('test', 'The quick brown fox jumped over the lazy dog.');
    $output = $page->capture();
    $this->assertEqual($output, "The quick\nbrown fox\njumped\nover the\nlazy dog.");
  }

  function testWordWrapSet()
  {
    $template = '<core:SET test="The quick brown fox jumped over the lazy dog."/>{$test|wordwrap:10}';
    $this->registerTestingTemplate('/filters/core/wordwrap/set.html', $template);

    $page = $this->initTemplate('/filters/core/wordwrap/set.html');
    $output = $page->capture();
    $this->assertEqual($output, "The quick\nbrown fox\njumped\nover the\nlazy dog.");
  }
}
?>