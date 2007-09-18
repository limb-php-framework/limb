<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

//temporary includes, make it more flexible later
lmb_require('limb/macro/src/lmbMacroTagDictionary.class.php');
lmb_require('limb/macro/src/lmbMacroTagInfo.class.php');
lmb_require('limb/macro/src/lmbMacroTag.class.php');

lmbMacroTagDictionary :: instance()->register(new lmbMacroTagInfo('slot', 'lmbMacroSlotTag'), __FILE__);

/**
 * class lmbMacroSlotTag.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroSlotTag extends lmbMacroTag
{
  function generateContents($code)
  {
    $slot = $this->getId();
    //calling slot handler in case of dynamic wrapping
    $code->writePHP('if(isset($this->__slot_handler_' . $slot . ')) {');
    $code->writePHP('call_user_func_array($this->__slot_handler_' . $slot . ', array());');
    $code->writePHP('}');

    //we need to isolate statically wrapped template variables via method call
    //in case of dynamic call we don't have children, hence the check
    if($this->children)
    {
      $method = $code->beginMethod('__slotHandler' . uniqid());
      parent :: generateContents($code);
      $code->endMethod();
      $code->writePHP('$this->' . $method . '()');
    }
  }
}

