<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/compiler/property/WactPropertyDictionary.class.php');
require_once('limb/wact/src/compiler/property/WactCompilerProperty.class.php');

class WactSomePropertyTagClass
{
}

class WactSomeChildPropertyTagClass extends WactSomePropertyTagClass
{
}


class WactPropertyDictionaryTest extends UnitTestCase
{
  function testGetPropertyList()
  {
    $dict = new WactPropertyDictionary();
    $info = new WactPropertyInfo('name', 'WactSomePropertyTagClass', 'NoClass');
    $dict->registerPropertyInfo($info, $file = 'whatever');
    $test_tag_list = array('name' => $info);

    $tag = new WactSomeChildPropertyTagClass();
    $this->assertEqual($dict->getPropertyList($tag), $test_tag_list);
  }

  function testGetPropertyListEmpty()
  {
    $dict = new WactPropertyDictionary();
    $info = new WactPropertyInfo('name', 'WactSomeChildPropertyTagClass', 'NoClass');
    $dict->registerPropertyInfo($info, $file = 'whatever');
    $test_tag_list = array('name' => $info);

    $tag = new WactSomePropertyTagClass();
    $this->assertEqual($dict->getPropertyList($tag), array());
  }

  function testRegisterPropertyInfoOnceOnly()
  {
    $dictionary = new WactPropertyDictionary();
    $info1 = new WactPropertyInfo('name', 'WactSomePropertyTagClass', 'NoClass');
    $info2 = new WactPropertyInfo('name', 'WactSomePropertyTagClass', 'NoClass');
    $dictionary->registerPropertyInfo($info1, $file1 = 'whatever1');
    $dictionary->registerPropertyInfo($info2, $file2 = 'whatever2');

    $tag = new WactSomeChildPropertyTagClass();
    $this->assertEqual($dictionary->getPropertyList($tag), array('name' => $info1));
  }

  function testgetPropertyListForParentAndChildTag()
  {
    $dictionary = new WactPropertyDictionary();
    $info1 = new WactPropertyInfo('name1', 'WactSomePropertyTagClass', 'NoClass');
    $info2 = new WactPropertyInfo('name2', 'WactSomeChildPropertyTagClass', 'NoClass');
    $dictionary->registerPropertyInfo($info1, $file1 = 'whatever1');
    $dictionary->registerPropertyInfo($info2, $file2 = 'whatever2');

    $tag = new WactSomeChildPropertyTagClass();
    $this->assertEqual($dictionary->getPropertyList($tag), array('name1' => $info1,
                                                                 'name2' => $info2));
  }
}

