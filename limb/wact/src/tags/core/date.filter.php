<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: date.filter.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
 * @filter date
 * @max_attributes 1
 */
class WactDateFilter extends WactCompilerFilter
{
   function getValue()
   {
     if ($this->isConstant())
     {
       $value = $this->base->getValue();
       $exp = $this->parameters[0]->getValue();
       return date($exp, $value);
     } else {
       $this->raiseUnresolvedBindingError();
     }
   }

   /**
    * @param WactCodeWriter
    */
   function generateExpression($code_writer)
   {
     $code_writer->writePHP('date(');
     $this->parameters[0]->generateExpression($code_writer);
     $code_writer->writePHP(',');
     $this->base->generateExpression($code_writer);
     $code_writer->writePHP(')');
   }
}
?>
