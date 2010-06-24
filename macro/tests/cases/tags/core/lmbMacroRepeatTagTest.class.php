<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroRepeatTagTest extends lmbBaseMacroTest
{
  function testRepeatTimesIsStaticNumber()
  {
    $template = '{{repeat times="3"}}F{{/repeat}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEqual($page->render(), 'FFF');
  }
  
  function testRepeatTimesIsVariableValue()
  {
    $template = '{{repeat times="$#count"}}F{{/repeat}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('count', 2);
    
    $this->assertEqual($page->render(), 'FF');
  }
}

