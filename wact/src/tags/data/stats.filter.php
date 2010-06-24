<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @filter stats
 * @min_attributes 1
 * @max_attributes 2
 * @package wact
 * @version $Id: stats.filter.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactStatsFilter extends WactCompilerFilter {

    /**
     * Return this value as a PHP value
     * @return String
     * @access public
     */
    function getValue() {
      if ($this->isConstant()) {
        $value = $this->base->getValue();
        $id = $this->parameters[0]->getValue();
        if (array_key_exists(1,$this->parameters)) {
          $mode = $this->parameters[1]->getValue();
          $code_writer->writeHTML(wact_stats_filter($value, $id, $mode));
        } else {
          $code_writer->writeHTML(wact_stats_filter($value, $id));
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
    function generateExpression($code_writer) {
      $code_writer->registerInclude('limb/wact/src/components/data/stats_filter.inc.php');
      $code_writer->writePHP('wact_stats_filter(');
      $this->base->generateExpression($code_writer);
      $code_writer->writePHP(',');
      $this->parameters[0]->generateExpression($code_writer);
      if (array_key_exists(1,$this->parameters)) {
        $code_writer->writePHP(',');
        $this->parameters[1]->generateExpression($code_writer);
      }
      $code_writer->writePHP(')');
    }

}


