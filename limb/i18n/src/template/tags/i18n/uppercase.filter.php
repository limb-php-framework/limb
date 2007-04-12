<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: uppercase.filter.php 5646 2007-04-12 08:38:15Z pachanga $
 * @package    i18n
 */
/**
* @filter i18n_uppercase
*/
class lmbI18NUppercaseFilter extends WactCompilerFilter
{
  var $locale_var;

  function getValue()
  {
    $value = $this->base->getValue();

    $toolkit = lmbToolkit :: instance();

    if($this->isConstant())
      return lmb_strtoupper($value);
    else
      $this->raiseUnresolvedBindingError();
  }

  function generateExpression($code)
  {
    $code->writePHP('lmb_strtoupper(');
    $this->base->generateExpression($code);
    $code->writePHP(')');
  }
}

?>