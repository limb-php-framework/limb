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
  const DEFAULT_ROLE = 'lmbToolkit::instance()->getMember()';

  protected function _generateContent($code)
  {
    $code->writePHP("if(lmbToolkit::instance()->getAcl()->isAllowed(");

    if(!$role = $this->getEscaped('role'))
      $role = self::DEFAULT_ROLE;
    $code->writePHP($role);

    $code->writePHP(', '.$this->getEscaped('resource'));

    if($privilege = $this->getEscaped('privelege'))
      $code->writePHP(', '.$privilege);

    if($privilege = $this->getEscaped('privilege'))
      $code->writePHP(', '.$privilege);

    $code->writePHP(')) {'.PHP_EOL);
    parent :: _generateContent($code);
    $code->writePHP('}'.PHP_EOL);
  }
}
