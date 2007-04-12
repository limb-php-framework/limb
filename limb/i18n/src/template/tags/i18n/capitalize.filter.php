<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: capitalize.filter.php 5646 2007-04-12 08:38:15Z pachanga $
 * @package    i18n
 */
/**
* @filter i18n_capitalize
*/
class lmbI18NCapitalizeFilter extends WactCompilerFilter
{
  var $locale_var;

  function getValue()
  {
    $value = $this->base->getValue();

    $toolkit = lmbToolkit :: instance();

    if ($this->isConstant())
      return lmb_ucfirst($value);
    else
      $this->raiseUnresolvedBindingError();
  }

  function generateExpression($code)
  {
    parent :: generateExpression($code);

    $code->writePHP('lmb_ucfirst(');
    $this->base->generateExpression($code);
    $code->writePHP(')');
  }
}

?>