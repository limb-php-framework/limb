<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: math.filter.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
 * @filter math
 * @min_attributes 1
 * @max_attributes 9999
 */

require_once('limb/wact/src/components/data/math_filter.inc.php');

class WactMathFilter extends WactCompilerFilter {

    /**
     * Return this value as a PHP value
     * @return String
     */
    function getValue() {
      if ($this->isConstant()) {
        $value = $this->base->getValue();
        $exp = '';
        foreach (array_keys($this->parameters) as $i) {
          $exp .= $this->parameters[$i]->getValue();
        }
        return math_filter($value,
                           $exp,
                           $this->location_in_template->getFile(),
                           $this->location_in_template->getLine());
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
    function generateExpression($code_writer) {
      $code_writer->registerInclude('limb/wact/src/components/data/math_filter.inc.php');
      $code_writer->writePHP('math_filter(');
      $this->base->generateExpression($code_writer);
      $code_writer->writePHP(',');
      $first = true;
      foreach (array_keys($this->parameters) as $i) {
        if (!$first) {
          $code_writer->writePHP('.');
        } else {
          $first = false;
        }
        $this->parameters[$i]->generateExpression($code_writer);
      }
      $code_writer->writePHP(',');
      $code_writer->writePHPLiteral($this->location_in_template->getFile());
      $code_writer->writePHP(',');
      $code_writer->writePHPLiteral($this->location_in_template->getLine());
      $code_writer->writePHP(')');
    }
}
?>