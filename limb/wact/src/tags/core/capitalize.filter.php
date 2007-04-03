<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: capitalize.filter.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
 * @filter capitalize
 * @version $Id: capitalize.filter.php 5021 2007-02-12 13:04:07Z pachanga $
 */
class WactCapitalizeFilter extends WactCompilerFilter {

    /**
     * Return this value as a PHP value
     * @return String
     * @access public
     */
    function getValue()
    {
      if ($this->isConstant())
        return ucfirst($this->base->getValue());
      else
        $this->raiseUnresolvedBindingError();
    }

    /**
     * Generate the code to read the data value at run time
     * Must generate only a valid PHP Expression.
     * @param WactCodeWriter
     * @return void
     */
    function generateExpression($code_writer)
    {
      $code_writer->writePHP('ucfirst(');
      $this->base->generateExpression($code_writer);
      $code_writer->writePHP(')');
    }

}

?>
