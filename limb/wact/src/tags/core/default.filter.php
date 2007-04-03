<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: default.filter.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
 * @filter default
 * @min_attributes 1
 * @max_attributes 1
 * @version $Id: default.filter.php 5021 2007-02-12 13:04:07Z pachanga $
 */
class WactDefaultFilter extends WactCompilerFilter
{
  /**
   * Return this value as a PHP value
   * @return String
   * @access public
   */
  function getValue() {
    if ($this->isConstant())
    {
      $value = $this->base->getValue();
      if (empty($value) && $value !== "0" && $value !== 0)
      {
        return $this->parameters[0]->getValue();
      } else {
        return $value;
      }
    } else {
      $this->raiseUnresolvedBindingError();
    }
  }

  /**
   * Generate the code to read the data value at run time
   * Must generate only a valid PHP Expression.
   * @param WactCodeWriter
   * @return void
   */
  function generateExpression($code_writer)
  {
    $code_writer->registerInclude('limb/wact/src/components/core/default_filter.inc.php');
    $code_writer->writePHP('WactApplyDefault(');
    $this->base->generateExpression($code_writer);
    $code_writer->writePHP(',');
    $this->parameters[0]->generateExpression($code_writer);
    $code_writer->writePHP(')');
  }
}

?>
