<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbMacroSlotTag.
 *
 * @tag slot
 * @forbid_end_tag    
 * @package macro
 * @version $Id$
 */
class lmbMacroSlotTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $slot = $this->getNodeId();
    //calling slot handler in case of dynamic wrapping
    $code->writePHP('if(isset($this->__slot_handlers_' . $slot . ')) {');
    $arg_str = $this->attributesIntoArrayString($skip = array('id', 'inline'));
    $code->writePHP('foreach($this->__slot_handlers_' . $slot . ' as $__slot_handler_' . $slot . ') {');
    $code->writePHP('call_user_func_array($__slot_handler_' . $slot . ', array(' . $arg_str . '));');
    $code->writePHP('}}');

    if(!$this->getBool('inline'))
    {
      $args = $code->generateVar(); 
      $method = $code->beginMethod('__slotHandler' . self::generateUniqueId(), array($args . '= array()'));
      
      $code->writePHP("if($args) extract($args);");
      
      parent :: _generateContent($code);
      
      $code->endMethod();
      //$arg_str = $this->attributesIntoArrayString($skip = array('id', 'inline'));
      $code->writePHP('$this->' . $method . '(' . $arg_str . ');');
    }
    else
      parent :: _generateContent($code);
  }
}

