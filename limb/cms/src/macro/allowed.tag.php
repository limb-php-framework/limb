<?php
lmb_require('limb/macro/src/compiler/lmbMacroNode.class.php');
lmb_require('limb/macro/src/compiler/lmbMacroTag.class.php');
lmb_require('limb/acl/src/lmbAcl.class.php');
/**
 * class AllowedTag.
 * @tag allowed
 * @req_attributes resource
 * @restrict_self_nesting
 */
class AllowedTag extends lmbMacroTag
{
  protected $_storage;
  const default_role = 'lmbToolkit::instance()->getMember()';  

  protected function _generateContent($code)
  {
    $code->writePHP("if(lmbToolkit::instance()->getAcl()->isAllowed(");
    
    if(!$role = $this->getEscaped('role'))
      $role = self::default_role;
    $code->writePHP($role);
    
    $code->writePHP(', '.$this->getEscaped('resource'));
        
    if($privelege = $this->getEscaped('privelege'))
      $code->writePHP(', '.$privelege);
      
    $code->writePHP(')) {'.PHP_EOL);          
    parent :: _generateContent($code);
    $code->writePHP('}'.PHP_EOL);
  }
}
