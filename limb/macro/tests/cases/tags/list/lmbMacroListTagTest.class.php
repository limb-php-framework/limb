<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroListTagTest extends lmbBaseMacroTest
{
  function testSimpleList()
  {
    $list = '{{list using="$#list" as="$item"}}{{list:item}}<?=$item?> {{/list:item}}{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd'));

    $out = $macro->render();
    $this->assertEqual($out, 'Bob Todd ');
  }

  function testListUsingDefaultItem()
  {
    $list = '{{list using="$#list"}}{{list:item}}<?=$item?> {{/list:item}}{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd'));

    $out = $macro->render();
    $this->assertEqual($out, 'Bob Todd ');
  }

  function testEmptyList()
  {
    $list = '{{list using="$#list" as="$item"}}{{list:item}}<?=$item?>{{/list:item}}' .
            '{{list:empty}}Nothing{{/list:empty}}{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array());

    $out = $macro->render();
    $this->assertEqual($out, 'Nothing');
  }

  function testShowCounter()
  {
    $list = '{{list using="$#list" counter="$ctr"}}{{list:item}}<?=$ctr?>)<?=$item?> {{/list:item}}{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd'));

    $out = $macro->render();
    $this->assertEqual($out, '1)Bob 2)Todd ');
  }

  function testTextNodesInsideListTag()
  {
    $list = '{{list using="$#list" as="$item"}}List: {{list:item}}<?=$item?> {{/list:item}} !{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd'));

    $out = $macro->render();
    $this->assertEqual($out, 'List: Bob Todd  !');
  }

  function testTextNodesInsideListTagWithEmptyListTag()
  {
    $list = '{{list using="$#list" as="$item"}}List: {{list:item}}<?=$item?> {{/list:item}} !' .
            '{{list:empty}}Nothing{{/list:empty}}{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array());

    $out = $macro->render();
    $this->assertEqual($out, 'Nothing');
  }

  function testParity()
  {
    $list = '{{list using="$#list" as="$item" parity="$parity"}}{{list:item}}{$parity}-{$item} {{/list:item}} !{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd', 'Jeff'));

    $out = $macro->render();
    $this->assertEqual($out, 'odd-Bob even-Todd odd-Jeff  !');
  }

  function testEvenAndOddTags()
  {
    $list = '{{list using="$#list" as="$item" parity="$parity"}}{{list:item}}'.
              '{{list:odd}}Odd{{/list:odd}}{{list:even}}Even{{/list:even}}-{$item} {{/list:item}} !{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd', 'Jeff'));

    $out = $macro->render();
    $this->assertEqual($out, 'Odd-Bob Even-Todd Odd-Jeff  !');
  }

  function testListWithGlue()
  {
    $list = '{{list using="$#list" as="$item"}}List:'.
            '{{list:item}}<?=$item?>{{list:glue}}||{{/list:glue}}'.
            '{{/list:item}}!' .
            '{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd', 'Marry'));

    $out = $macro->render();
    $this->assertEqual($out, 'List:Bob||Todd||Marry!');
  }

  function testListWithGlueWithStep()
  {
    $list = '{{list using="$#list" as="$item"}}List:'.
            '{{list:item}}<?=$item?>{{list:glue step="2"}}||{{/list:glue}}'.
            '{{/list:item}}!' .
            '{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd', 'Marry'));

    $out = $macro->render();
    $this->assertEqual($out, 'List:BobTodd||Marry!');
  }

  function testTwoDependentGlues()
  {
    $list = '{{list using="$#list" as="$item"}}List#'.
            '{{list:item}}<?=$item?>' .
            '{{list:glue step="2"}}|{{/list:glue}}'.
            '{{list:glue}}:{{/list:glue}}'.
            '{{/list:item}}!'.
            '{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('John', 'Pavel', 'Peter', 'Harry', 'Roman', 'Sergey'));

    $this->assertEqual($macro->render(), 'List#John:Pavel|Peter:Harry|Roman:Sergey!');
  }

  function testListFillTagWithRatio()
  {
    $list = '{{list using="$#list" as="$item"}}List#'.
                '{{list:item}}{$item}'.
                '{{list:glue step="3"}}++{{/list:glue}}'.
                '{{list:glue}}:{{/list:glue}}'.
                '{{/list:item}}'.
                '{{list:fill upto="3" items_left="$items_left"}}{$items_left}{{/list:fill}}'.
                '{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('John', 'Pavel', 'Peter', 'Harry'));

    $this->assertEqual($macro->render(), 'List#John:Pavel:Peter++Harry2');
  }

  function testListFillTagWithTotalElementsLessThanRatioDoesNotRenderAnything()
  {
    $list = '{{list using="$#list" as="$item"}}List#'.
                '{{list:item}}{$item}'.
                '{{list:glue step="3"}}++{{/list:glue}}'.
                '{{list:glue}}:{{/list:glue}}'.
                '{{/list:item}}'.
                '{{list:fill upto="3" items_left="$items_left"}}{$items_left}{{/list:fill}}'.
                '{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('John', 'Pavel'));

    $this->assertEqual($macro->render(), 'List#John:Pavel');
  }

  function testListFillTagWithTotalElementsLessButWithForceAttributeIsRendering()
  {
    $list = '{{list using="$#list" as="$item"}}List#'.
            '{{list:item}}{$item}'.
            '{{list:glue step="3"}}++{{/list:glue}}'.
            '{{list:glue}}:{{/list:glue}}'.
            '{{/list:item}}'.
            '{{list:fill upto="3" force="true" items_left="$items_left"}}{$items_left}{{/list:fill}}'.
            '{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('John', 'Pavel'));

    $this->assertEqual($macro->render(), 'List#John:Pavel1');
  }

  function testListFillTagWithTotalElementsLessButWithForceAttributeButWithEmptyList()
  {
    $list = '{{list using="$#list" as="$item"}}List#'.
            '{{list:item}}{$item}'.
            '{{list:glue step="3"}}++{{/list:glue}}'.
            '{{list:glue}}:{{/list:glue}}'.
            '{{/list:item}}'.
            '{{list:fill upto="3" force="true" items_left="$items_left"}}{$items_left}{{/list:fill}}'.
            '{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array());

    $this->assertEqual($macro->render(), '');
  }
}

