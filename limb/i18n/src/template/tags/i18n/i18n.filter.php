<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: i18n.filter.php 5848 2007-05-09 12:32:31Z pachanga $
 * @package    i18n
 */
/**
* @filter i18n
* @min_attributes 1
* @max_attributes 100
*/
class lmbI18NStringFilter extends WactCompilerFilter
{
  function getValue()
  {
    if(!isset($this->parameters[0]) || !$this->parameters[0]->getValue())
      throw new WactException('MISSING_FILTER_PARAMETER');
    else
      $domain = $this->parameters[0]->getValue();

    $value = $this->base->getValue();

    if($this->isConstant())
      return lmb_i18n($value, $this->_getAttributes(), $domain);
    else
      $this->raiseUnresolvedBindingError();
  }

  function _getAttributes()
  {
    $result = array();

    for($i=1; $i < sizeof($this->parameters); $i+=2)
    {
      $var = $this->parameters[$i]->getValue();
      $value = $this->parameters[$i+1]->getValue();
      $result[$var] = $value;
    }

    return $result;
  }

  function generateExpression($code)
  {
    throw new lmbException('DBE mode is not supported in i18n filter');
  }
}

?>