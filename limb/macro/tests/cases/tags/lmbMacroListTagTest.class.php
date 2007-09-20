<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/fs/src/lmbFs.class.php');
lmb_require('limb/macro/src/lmbMacroTemplate.class.php');
lmb_require('limb/macro/src/lmbMacroTagDictionary.class.php');

lmbMacroTagDictionary :: instance()->registerFromFile(dirname(__FILE__) . '/../../../src/tags/list.tag.php');
lmbMacroTagDictionary :: instance()->registerFromFile(dirname(__FILE__) . '/../../../src/tags/list_item.tag.php');
lmbMacroTagDictionary :: instance()->registerFromFile(dirname(__FILE__) . '/../../../src/tags/list_empty.tag.php');
lmbMacroTagDictionary :: instance()->registerFromFile(dirname(__FILE__) . '/../../../src/tags/list_glue.tag.php');

class lmbMacroListTagTest extends UnitTestCase
{
  function setUp()
  {
    lmbFs :: rm(LIMB_VAR_DIR . '/tpl');
    lmbFs :: mkdir(LIMB_VAR_DIR . '/tpl/compiled');
  }

  function testSimpleList()
  {
    $list = '<%list using="$#list" as="$item"%><%list:item%><?=$item?> <%/list:item%><%/list%>';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd'));

    $out = $macro->render();
    $this->assertEqual($out, 'Bob Todd ');
  }

  function testListUsingDefaultItem()
  {
    $list = '<%list using="$#list"%><%list:item%><?=$item?> <%/list:item%><%/list%>';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd'));

    $out = $macro->render();
    $this->assertEqual($out, 'Bob Todd ');
  }

  function testEmptyList()
  {
    $list = '<%list using="$#list" as="$item"%><%list:item%><?=$item?><%/list:item%>' . 
            '<%list:empty%>Nothing<%/list:empty%><%/list%>';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array());

    $out = $macro->render();
    $this->assertEqual($out, 'Nothing');
  }

  function testShowCounter()
  {
    $list = '<%list using="$#list" counter="$ctr"%><%list:item%><?=$ctr?>)<?=$item?> <%/list:item%><%/list%>';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd'));

    $out = $macro->render();
    $this->assertEqual($out, '1)Bob 2)Todd ');
  }

  function testTextNodesInsideListTag()
  {
    $list = '<%list using="$#list" as="$item"%>List: <%list:item%><?=$item?> <%/list:item%> !<%/list%>';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd'));

    $out = $macro->render();
    $this->assertEqual($out, 'List: Bob Todd  !');
  }

  function testTextNodesInsideListTagWithEmptyListTag()
  {
    $list = '<%list using="$#list" as="$item"%>List: <%list:item%><?=$item?> <%/list:item%> !' . 
            '<%list:empty%>Nothing<%/list:empty%><%/list%>';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array());

    $out = $macro->render();
    $this->assertEqual($out, 'Nothing');
  }

  function testListWithGlue()
  {
    $list = '<%list using="$#list" as="$item"%>List:<%list:item%><?=$item?><%/list:item%>!' . 
            '<%list:glue%>||<%/list:glue%><%/list%>';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd', 'Marry'));

    $out = $macro->render();
    $this->assertEqual($out, 'List:Bob||Todd||Marry!');
  }

  protected function _createMacro($file)
  {
    $base_dir = LIMB_VAR_DIR . '/tpl';
    $cache_dir = LIMB_VAR_DIR . '/tpl/compiled';
    $macro = new lmbMacroTemplate($file,
                                  $cache_dir,
                                  new lmbMacroTemplateLocator($base_dir, $cache_dir));
    return $macro;
  }

  protected function _createTemplate($code, $name)
  {
    $file = LIMB_VAR_DIR . '/tpl/' . $name;
    file_put_contents($file, $code);
    return $file;
  }
}

