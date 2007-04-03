<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: ip.filter.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
/**
* @filter ip
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
    $code->registerInclude('http/lmbIp.class.php');

    $code->writePHP('lmbIp :: decode(');
    $this->base->generateExpression($code);
    $code->writePHP(')');
  }
}

?>