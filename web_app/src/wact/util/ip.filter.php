<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
/**
 * @filter ip
 * @package web_app
 * @version $Id: ip.filter.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbIpFilter extends WactCompilerFilter
{
  function getValue()
  {
    lmb_require('limb/net/src/lmbIp.class.php');
    if ($this->isConstant())
      return lmbIp :: decode($this->base->getValue());
    else
      $this->raiseUnresolvedBindingError();
  }

  function generateExpression($code)
  {
    $code->registerInclude('limb/net/src/lmbIp.class.php');

    $code->writePHP('lmbIp :: decode(');
    $this->base->generateExpression($code);
    $code->writePHP(')');
  }
}


