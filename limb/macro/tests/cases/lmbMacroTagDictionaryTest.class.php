<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/macro/src/lmbMacroTag.class.php');
lmb_require('limb/macro/src/lmbMacroTagInfo.class.php');
lmb_require('limb/macro/src/lmbMacroTagDictionary.class.php');

class lmbMacroTagDictionaryTest extends UnitTestCase
{
  protected $dictionary;
  protected $tag_info;

  function setUp()
  {
    $this->tag_info = new lmbMacroTagInfo('testtag', 'SomeTagClass');
    $this->dictionary = new lmbMacroTagDictionary();
    $this->dictionary->register($this->tag_info, $file = 'whaever');
  }

  protected function _createParentTag()
  {
    $tag_info = new lmbMacroTagInfo('some_tag', 'SomeTagClass');
    return new lmbMacroTag(new lmbMacroSourceLocation('file', '10'), 'some_tag', $tag_info);
  }

  function testFindTagInfo()
  {
    $this->assertIsA($this->dictionary->findTagInfo('testtag'), 'lmbMacroTagInfo');
  }

  function testRegisterTagInfoOnceOnly()
  {
    $dictionary = new lmbMacroTagDictionary();
    $tag_info1 = new lmbMacroTagInfo('some_tag', 'SomeTagClass');
    $tag_info2 = new lmbMacroTagInfo('some_tag', 'SomeTagClass');
    $dictionary->register($tag_info1, $file1 = 'whaever1');
    $dictionary->register($tag_info2, $file2 = 'whaever2');

    $this->assertEqual($dictionary->findTagInfo('some_tag'), $tag_info1);
  }

  function testNotATag()
  {
    $parent = $this->_createParentTag();
    $tag = 'notatag';
    $attrs = array();
    $this->assertFalse($this->dictionary->findTagInfo($tag, $attrs, FALSE, $parent));
  }
}
?>