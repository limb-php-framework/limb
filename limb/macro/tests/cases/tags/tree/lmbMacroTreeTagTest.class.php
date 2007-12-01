<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroTreeTagTest extends lmbBaseMacroTest
{
  function testRenderTree()
  {
    $content = '{{tree using="$#tree" as="$item" kids_prop="kids"}}' . 
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
}

