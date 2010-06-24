<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/compiler/templatecompiler.inc.php');

class WactTagDictionaryTest extends UnitTestCase
{
  protected $dictionary;
  protected $tag_info;

  function setUp()
  {
    $this->tag_info = new WactTagInfo('testtag', 'SomeTagClass');
    $this->dictionary = new WactTagDictionary();
    $this->dictionary->registerWactTagInfo($this->tag_info, $file = 'whaever');
  }

  protected function _createParentTag()
  {
    $tag_info = new WactTagInfo('some_tag', 'SomeTagClass');
    return new WactRuntimeComponentHTMLTag(new WactSourceLocation('file', '10'), 'some_tag', $tag_info);
  }

  function testGetWactTagInfo()
  {
    $this->assertIsA($this->dictionary->getWactTagInfo('testtag'), 'WactTagInfo');
  }

  function testRegisterTagInfoOnceOnly()
  {
    $dictionary = new WactTagDictionary();
    $tag_info1 = new WactTagInfo('some_tag', 'SomeTagClass');
    $tag_info2 = new WactTagInfo('some_tag', 'SomeTagClass');
    $dictionary->registerWactTagInfo($tag_info1, $file1 = 'whaever1');
    $dictionary->registerWactTagInfo($tag_info2, $file2 = 'whaever2');

    $this->assertEqual($dictionary->getWactTagInfo('some_tag'), $tag_info1);
  }

  function testNotATag()
  {
    $parent = $this->_createParentTag();
    $tag = 'notatag';
    $attrs = array();
    $this->assertFalse($this->dictionary->findTagInfo($tag, $attrs, FALSE, $parent));
  }

  function testRunatEqualsClient()
  {
    $parent = $this->_createParentTag();
    $tag = 'foo';
    $attrs = array('runat' => 'client');
    $this->assertNull($this->dictionary->findTagInfo($tag, $attrs, FALSE, $parent));
  }

  function testComponentWithRunatServer()
  {
    $parent = $this->_createParentTag();
    $this->dictionary->registerWactTagInfo($info = new WactTagInfo('isatag', 'blah'), $file = 'whatever');
    $tag = 'iSaTag';
    $attrs = array('runat'=>'server');
    $this->assertEqual($this->dictionary->findTagInfo($tag, $attrs, FALSE, $parent), $info);
  }

  function testWactGenericHTMLTag()
  {
    $parent = $this->_createParentTag();
    $tag = 'iSaWactGenericHTMLTag';
    $attrs = array('runat' => 'server');
    $this->assertIsA($this->dictionary->findTagInfo($tag, $attrs, TRUE, $parent), 'WactTagInfo');
  }

  function testWactGenericContainerHTMLTag()
  {
    $parent = $this->_createParentTag();
    $tag = 'p';
    $attrs = array('runat' => 'server');
    $info = $this->dictionary->findTagInfo($tag, $attrs, FALSE, $parent);
    $this->assertEqual($info->TagClass, 'WactGenericContainerHTMLTag');
    $this->assertEqual($info->Tag, 'p');
  }

  function testRunatAsHasMorePriority()
  {
    $parent = $this->_createParentTag();

    $info = new WactTagInfo('my_tag', 'blah');
    $info->setRunat('client');
    $info->setRunatAs('WactRuntimeComponentHTMLTag');

    $this->dictionary->registerWactTagInfo($info, $file = 'whatever');

    $tag = 'my_tag';
    $attrs = array();

    $this->assertEqual($this->dictionary->findTagInfo($tag, $attrs, FALSE, $parent), $info);
  }

}

