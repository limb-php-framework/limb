<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/cms/src/model/lmbCmsTextBlock.class.php');
lmb_require('limb/cms/tests/cases/lmbCmsTestCase.class.php');

class lmbCmsTextBlockTest extends lmbCmsTestCase
{
  function testGetRawContent_Positive()
  {
    $block = new lmbCmsTextBlock();
    $block->setIdentifier($identifier = 'foo');
    $block->setContent($content = '<p>bar</p>');
    $block->save();
    
    $block_content = lmbCmsTextBlock::getRawContent($identifier);
    $this->assertEqual($block_content, $content);
  }
  
  function testGetRawContent_Negative()
  {    
    $block_content = lmbCmsTextBlock::getRawContent('not_existed');
    $this->assertEqual($block_content, '');
  }
}