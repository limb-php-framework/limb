<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');

/**
 * Checks that field value is an alpha-numeric string
 * @package validation
 * @version $Id: lmbIdentifierRule.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbIdentifierRule extends lmbSingleFieldRule
{
  function check($value)
  {
    $value = "$value";

    if (!preg_match("/^[a-zA-Z0-9.-]+$/i", $value))
        $this->error('{Field} must contain only letters and numbers');
  }
}

