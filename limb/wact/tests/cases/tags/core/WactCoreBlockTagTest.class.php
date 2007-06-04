<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactCoreBlockTagTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

class WactCoreBlockTagTest extends WactTemplateTestCase
{
  function setUp()
  {
    parent :: setUp();

    $template = 'Before-<core:block id="block">Block</core:block>-After';
    $this->registerTestingTemplate('/tags/core/block/block.html', $template);

    $template = 'Before-<core:block id="block" hide="true">Block</core:block>-After';
    $this->registerTestingTemplate('/tags/core/block/hiddenblock.html', $template);
  }

  function testBlock()
  {
    $page = $this->initTemplate('/tags/core/block/block.html');
    $block = $page->getChild('block');
    $this->assertTrue($block->isVisible());
    $output = $page->capture();
    $this->assertEqual($output, 'Before-Block-After');
  }

  function testHiddenBlock()
  {
    $page = $this->initTemplate('/tags/core/block/hiddenblock.html');
    $block = $page->getChild('block');
    $this->assertFalse($block->isVisible());
    $output = $page->capture();
    $this->assertEqual($output, 'Before--After');
  }

  function testInvertBlock()
  {
    $page = $this->initTemplate('/tags/core/block/block.html');
    $block = $page->getChild('block');
    $this->assertTrue($block->isVisible());
    $block->Hide();
    $this->assertFalse($block->isVisible());
    $output = $page->capture();
    $this->assertEqual($output, 'Before--After');
  }

  function testInvertHiddenBlock()
  {
    $page = $this->initTemplate('/tags/core/block/hiddenblock.html');
    $block = $page->getChild('block');
    $this->assertFalse($block->isVisible());
    $block->Show();
    $this->assertTrue($block->isVisible());
    $output = $page->capture();
    $this->assertEqual($output, 'Before-Block-After');
  }
}
?>