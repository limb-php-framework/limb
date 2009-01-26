<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroSelectOptionsExportTagTest extends lmbBaseMacroTest
{
  function testExportOptions()
  {
    $template = '{{select_options_export from="$#source" to="$#options" key_field="id" text_field="name"}}' .
                '{{select name="select" options="$#options"/}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $page->set('source', array(array('id' => '4', 'name' => 'red'),
                                array('id' => '5', 'name' => 'blue')));

    $expected = '<select name="select"><option value="4">red</option><option value="5">blue</option></select>'; 
    $this->assertEqual($page->render(), $expected); 
  }
}

