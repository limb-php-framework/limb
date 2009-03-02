<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');

/**
 * class lmbTreeIdentifierRule.
 *
 * @package cms
 * @version $Id$
 */
class lmbTreeIdentifierRule extends lmbSingleFieldRule
{
  function check($value)
  {
    if(!preg_match('~^[a-zA-Z0-9-_\.]+$~', $value))
      return $this->error('{Field} может содержать только цифры, символы латинского алфавита и символы `-`, `_`, `.`');
  }
}


