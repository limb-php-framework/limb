<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class WactProcessingInstructionTest extends WactTemplateTestCase
{
  function testXmlProcessingInstruction()
  {
    $template = '<?xml version="1.0"?>';
    $this->registerTestingTemplate('/procinst/xml_processing_instruction.html', $template);

    $page = $this->initTemplate('/procinst/xml_processing_instruction.html');
    $this->assertEqual($page->capture(),$template."\n");
  }

  function testPHPProcessingInstruction()
  {
    $template = '<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
 echo "Foo"; ?>';
    $this->registerTestingTemplate('/procinst/php_processing_instruction.html', $template);

    $page = $this->initTemplate('/procinst/php_processing_instruction.html');
    $this->assertEqual($page->capture(),'Foo');
  }

  function testSeveralPHPBlocks()
  {
    $template = '<?php echo "Foo"; ?><tr><td><?php echo "Foo"; ?></td></tr>';
    $this->registerTestingTemplate('/procinst/several_php_blocks.html', $template);

    $page = $this->initTemplate('/procinst/several_php_blocks.html');
    $this->assertEqual($page->capture(), 'Foo<tr><td>Foo</td></tr>');
  }

  function testPHPWithHtmlBlocks()
  {
    $template = '<?php foreach(array("foo", "bar") as $item) { ?><b><?php echo $item; ?></b><?php } ?>';
    $this->registerTestingTemplate('/procinst/php_html_blocks.html', $template);

    $page = $this->initTemplate('/procinst/php_html_blocks.html');
    $this->assertEqual($page->capture(), '<b>foo</b><b>bar</b>');
  }
}

