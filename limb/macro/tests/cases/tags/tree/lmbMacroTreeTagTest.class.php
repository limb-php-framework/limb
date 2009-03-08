<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroTreeTagTest extends lmbBaseMacroTest
{
  function testRenderTree()
  {
    $content = '{{tree using="$#tree" kids_prop="kids"}}' .
                  '<ul>' .
                  '{{tree:node}}' .
                    '<li>{$item.title}{{tree:nextlevel/}}</li>' .
                  '{{/tree:node}}' .
                  '</ul>' .
                '{{/tree}}';

    $tpl = $this->_createTemplate($content, 'tree.html');

    $macro = $this->_createMacro($tpl);
    $macro->set('tree', array(array('title' => 'foo'),
                              array('title' => 'bar', 'kids' => array(array('title' => 'bar1'),
                                                                      array('title' => 'bar2'))),
                              array('title' => 'hey')));

    $out = $macro->render();
    $this->assertEqual($out, '<ul><li>foo</li><li>bar<ul><li>bar1</li><li>bar2</li></ul></li><li>hey</li></ul>');
  }

  function testCounter()
  {
    $content = '{{tree using="$#tree" kids_prop="kids" counter="$counter"}}' .
                  '<ul>' .
                  '{{tree:node}}' .
                  '<li>{$counter}){$item.title}'.
                  '{{tree:nextlevel/}}</li>' .
                  '{{/tree:node}}' .
                  '</ul>' .
                '{{/tree}}';

    $tpl = $this->_createTemplate($content, 'tree.html');

    $macro = $this->_createMacro($tpl);
    $macro->set('tree', array(array('title' => 'foo'),
                              array('title' => 'bar', 'kids' => array(array('title' => 'bar1'),
                                                                      array('title' => 'bar2'))),
                              array('title' => 'hey')));

    $out = $macro->render();
    $this->assertEqual($out, '<ul><li>1)foo</li><li>2)bar<ul><li>1)bar1</li><li>2)bar2</li></ul></li><li>3)hey</li></ul>');
  }

  function testPassExtraParamsIntoTreeMethod()
  {
    $content = '{{tree using="$#tree" as="$node" kids_prop="kids" counter="$counter" prefix="1"}}' .
                  '<ul>' .
                  '{{tree:node}}' .
                  '<li>{$prefix}.{$counter}){$node.title}'.
                  '{{tree:nextlevel prefix="$new_prefix"}}<?php $new_prefix = $prefix . "." . $counter; ?>{{/tree:nextlevel}}</li>' .
                  '{{/tree:node}}' .
                  '</ul>' .
                '{{/tree}}';

    $tpl = $this->_createTemplate($content, 'tree.html');

    $macro = $this->_createMacro($tpl);
    $macro->set('tree', array(array('title' => 'foo'),
                              array('title' => 'bar', 'kids' => array(array('title' => 'bar1'),
                                                                      array('title' => 'bar2'))),
                              array('title' => 'hey')));

    $out = $macro->render();
    $this->assertEqual($out, '<ul><li>1.1)foo</li><li>1.2)bar<ul><li>1.2.1)bar1</li><li>1.2.2)bar2</li></ul></li><li>1.3)hey</li></ul>');
  }

  function testCheckBC()
  {
    $content = '{{tree using="$#tree" kids_prop="kids"}}' .
                  '<ul>' .
                  '{{tree:branch}}' .
                    '<li>{{tree:item}}{$item.title}{{/tree:item}}</li>' .
                  '{{/tree:branch}}' .
                  '</ul>' .
                '{{/tree}}';

    $tpl = $this->_createTemplate($content, 'tree.html');

    $macro = $this->_createMacro($tpl);
    $macro->set('tree', array(array('title' => 'foo'),
                              array('title' => 'bar', 'kids' => array(array('title' => 'bar1'),
                                                                      array('title' => 'bar2'))),
                              array('title' => 'hey')));

    $out = $macro->render();
    $this->assertEqual($out, '<ul><li>foo</li><li>bar<ul><li>bar1</li><li>bar2</li></ul></li><li>hey</li></ul>');
  }

  function testTreeWithoutData()
  {
    $content = '{{tree using="$#tree" kids_prop="kids"}}' .
                  '<ul>' .
                  '{{tree:node}}' .
                    '<li>{$item.title}{{tree:nextlevel/}}</li>' .
                  '{{/tree:node}}' .
                  '</ul>' .
                  '{{tree:empty}}kids not found{{/tree:empty}}' .
                '{{/tree}}';

    $tpl = $this->_createTemplate($content, 'tree.html');

    $macro = $this->_createMacro($tpl);
    $macro->set('tree', array());

    $out = $macro->render();
    $this->assertEqual($out, 'kids not found');
  }
}
