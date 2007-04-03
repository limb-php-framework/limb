<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lowercase.filter.php 5380 2007-03-28 12:16:15Z pachanga $
 * @package    web_app
 */
/**
* @filter i18n_lowercase
*/
class lmbI18NLowercaseFilter extends WactCompilerFilter
{
  var $locale_var;

  function getValue()
  {
    $value = $this->base->getValue();

    $toolkit = lmbToolkit :: instance();

    if ($this->isConstant())
      return lmb_strtolower($value);
    else
      $this->raiseUnresolvedBindingError();
  }

  function generateExpression($code)
  {
    $code->writePHP('lmb_strtolower(');
    $this->base->generateExpression($code);
    $code->writePHP(')');
  }
}

?>