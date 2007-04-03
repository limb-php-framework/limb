<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: stats.filter.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
 * @filter stats
 * @min_attributes 1
 * @max_attributes 2
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

?>