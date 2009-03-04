<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @filter math
 * @min_attributes 1
 * @max_attributes 9999
 * @package wact
 * @version $Id: math.filter.php 7686 2009-03-04 19:57:12Z korchasa $
 */


require_once('limb/wact/src/components/data/math_filter.inc.php');class WactMathFilter extends WactCompilerFilter {

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

