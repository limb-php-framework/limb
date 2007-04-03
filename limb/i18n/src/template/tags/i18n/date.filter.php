<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: date.filter.php 5373 2007-03-28 11:10:40Z pachanga $
 * @package    web_app
 */
lmb_require('limb/datetime/src/lmbDate.class.php');
lmb_require('limb/i18n/src/datetime/lmbDateFormat.class.php');

/**
* @filter i18n_date
* @max_attributes 5
*/
class lmbI18NDateFilter extends WactCompilerFilter
{
  var $date;

  var $locale_var;
  var $date_var;
  var $date_format_var;

  function getValue()
  {
    $value = $this->base->getValue();

    $toolkit = lmbToolkit :: instance();

    if(isset($this->parameters[0]) && $this->parameters[0]->getValue())
      $locale = $toolkit->getLocaleObject($this->parameters[0]->getValue());
    else
      $locale = $toolkit->getLocaleObject();

    $this->date = new lmbDate();

    $this->_setDate();

    if ($this->isConstant())
    {
      $format = new lmbDateFormat();
      return $format->toString($this->date, $this->_getFormat($locale), $locale);
    }
    else
      $this->raiseUnresolvedBindingError();
  }

  function _setDate()
  {
    if(isset($this->parameters[1]) && $this->parameters[1]->getValue())
      $date_type = $this->parameters[1]->getValue();
    else
      $date_type = 'stamp';

    $value = $this->base->getValue();
    switch($date_type)
    {
      case 'string':
        $this->date = new lmbDate($value);
      break;
      case 'stamp':
        $this->date = new lmbDate((int)$value);
      break;

      default:
        $this->date = new lmbDate($value);
      break;
    }
  }

  function _getFormat($locale)
  {
    if(isset($this->parameters[3]) && $this->parameters[3]->getValue())
      return $this->parameters[3]->getValue();

    if(isset($this->parameters[2]) && $this->parameters[2]->getValue())
      $format_type = $this->parameters[2]->getValue();
    else
      $format_type = 'short_date';

    $property = $format_type . '_format';
    return $locale->$property;
  }

  function generatePreStatement($code)
  {
    parent :: generatePreStatement($code);

    $toolkit_var = $code->getTempVarRef();
    $this->locale_var = $code->getTempVarRef();

    $code->writePHP("lmb_require('limb/datetime/src/lmbDate.class.php');");
    $code->writePHP("lmb_require('limb/i18n/src/datetime/lmbDateFormat.class.php');");
    $code->writePHP($toolkit_var . ' = lmbToolkit :: instance();' . "\n");
    $code->writePHP($this->locale_var . ' = ');

    if(isset($this->parameters[0]) && $this->parameters[0]->getValue())
    {
      $code->writePHP($toolkit_var . '->getLocaleObject("' . $this->parameters[0]->getValue(). '");');
    }
    else
    {
      $code->writePHP($toolkit_var . '->getLocaleObject();');
    }

    $this->date_var = $code->getTempVarRef();
    $this->date_format_var = $code->getTempVarRef();

    $code->writePHP($this->date_format_var . ' = new lmbDateFormat();');

    $this->_setDBEDate($code);

  }

  function _setDBEDate($code)
  {
    if(isset($this->parameters[1]) && $this->parameters[1]->getValue())
      $date_type = $this->parameters[1]->getValue();
    else
      $date_type = 'stamp';

    switch($date_type)
    {
      case 'stamp':
        $code->writePHP($this->date_var . ' = new lmbDate((int)');
        $this->base->generateExpression($code);
        $code->writePHP(');');
      break;

      case 'string':
        $code->writePHP($this->date_var . ' = new lmbDate(');
        $this->base->generateExpression($code);
        $code->writePHP(');');
      break;

      default:
        $code->writePHP($this->date_var . ' = new lmbDate((int)');
        $this->base->generateExpression($code);
        $code->writePHP(');');
      break;
    }
  }

  function generateExpression($code)
  {
    parent :: generateExpression($code);

    $code->writePHP($this->date_format_var . '->toString(' . $this->date_var . ', ');
    $this->_getDBEFormat($code);
    $code->writePHP(' ,' . $this->locale_var . ')');
  }

  function _getDBEFormat($code)
  {
    if(isset($this->parameters[3]) && $this->parameters[3]->getValue())
    {
      $code->writePHP('"' . $this->parameters[3]->getValue() . '"');
      return;
    }

    if(isset($this->parameters[2]) && $this->parameters[2]->getValue())
      $format_type = $this->parameters[2]->getValue();
    else
      $format_type = 'short_date';

    $property = $format_type . '_format';
    $code->writePHP($this->locale_var .'->' . $property);
  }
}

?>