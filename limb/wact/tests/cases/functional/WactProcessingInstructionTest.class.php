<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactProcessingInstructionTest.class.php 5235 2007-03-14 09:44:28Z serega $
 * @package    wact
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
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactProcessingInstructionTest.class.php 5235 2007-03-14 09:44:28Z serega $
 * @package    wact
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
?>