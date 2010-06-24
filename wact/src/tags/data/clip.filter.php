<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * substr wraper
 *
 * plus some nice features about wrapping at a word boundary
 * parameters are as follows:
 * - length - integer - required - how long to make the string
 * - start - integer - optional - where to start (0 offset)
 * - terminator - string - optional - what to append to the end, i.e. "..."
 * - word boundary - char - anything but first letter "n" treated as yes, trim at a word boundary
 * @filter clip
 * @min_attributes 1
 * @max_attributes 4
 * @package wact
 * @version $Id: clip.filter.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactClipFilter extends WactCompilerFilter
{
  protected $str;
  protected $strlen;
  protected $start;
  protected $len;
  protected $suffix;
  protected $match;

    /**
     * Return this value as a PHP value
     * @return String
     */
    function getValue() {
      if ($this->isConstant()) {
        $value = $this->base->getValue();
        switch (count($this->parameters)) {
          case 1:
            return substr($value, 0, $this->parameters[0]->getValue());
            break;
          case 2:
            return substr($value, $this->parameters[1]->getValue(), $this->parameters[0]->getValue());
            break;
          case 3:
            $suffix = (strlen($value) > $this->parameters[0]->getValue() + $this->parameters[1]->getValue())
            ? $this->parameters[2]->getValue()
            : '';
            return substr($value, $this->parameters[1]->getValue(), $this->parameters[0]->getValue()).$suffix;
            break;
          case 4:
            if (strtoupper(substr($this->parameters[3]->getValue(),0,1)) != 'N') {
              preg_match('~^(.{0,'.$this->parameters[0]->getValue().'}(?U)\w*)\b~ism', substr($value, $this->parameters[1]->getValue()), $match);
              $suffix = (strlen($match[1]) < $this->parameters[0]->getValue())
              ? ''
              : $this->parameters[2]->getValue();
              return $match[1].$suffix;
            } else {
              preg_match('~^(.{0,'.$this->parameters[0]->getValue().'})~ism', substr($value, $this->parameters[1]->getValue()), $match);
              $suffix = (strlen($match[1]) < $this->parameters[0]->getValue())
              ? ''
              : $this->parameters[2]->getValue();
              return $match[1].$suffix;
            }
            break;
          default:
            throw new WactException("Wrong filter params");
        }
      } else {
        $this->raiseUnresolvedBindingError();
      }
    }

    /**
     * Generate setup code for an expression reference
     * @param WactCodeWriter
     * @return void
     */
    function generatePreStatement($code_writer) {
      parent::generatePreStatement($code_writer);
      switch (count($this->parameters)) {
        case 3:
          $this->str = $code_writer->getTempVarRef();
          $this->strlen = $code_writer->getTempVarRef();
          $this->len = $code_writer->getTempVarRef();
          $this->start = $code_writer->getTempVarRef();
          $this->suffix = $code_writer->getTempVarRef();

          $code_writer->writePHP($this->str.'=');
          $this->base->generateExpression($code_writer);
          $code_writer->writePHP(';');

          $code_writer->writePHP($this->strlen.'=strlen('.$this->str.');');

          $code_writer->writePHP($this->len.'=');
          $this->parameters[0]->generateExpression($code_writer);
          $code_writer->writePHP(';');

          $code_writer->writePHP($this->start.'=');
          $this->parameters[1]->generateExpression($code_writer);
          $code_writer->writePHP(';');

          $code_writer->writePHP($this->suffix.'=('.$this->strlen.'>'.$this->start.'+'.$this->len.')?');
          $this->parameters[2]->generateExpression($code_writer);
          $code_writer->writePHP(':\'\';');
          break;
        case 4:
          $this->str = $code_writer->getTempVarRef();
          $this->strlen = $code_writer->getTempVarRef();
          $this->len = $code_writer->getTempVarRef();
          $this->start = $code_writer->getTempVarRef();
          $this->suffix = $code_writer->getTempVarRef();
          $this->match = $code_writer->getTempVarRef();
          $code_writer->writePHP($this->str.'=');
          $this->base->generateExpression($code_writer);
          $code_writer->writePHP(';'.$this->strlen.'=strlen('.$this->str.');');
          $code_writer->writePHP($this->len.'=');
          $this->parameters[0]->generateExpression($code_writer);
          $code_writer->writePHP(';'.$this->start.'=');
          $this->parameters[1]->generateExpression($code_writer);
          $code_writer->writePHP(';if (strtoupper(substr(');
          $this->parameters[3]->generateExpression($code_writer);
          $code_writer->writePHP(',0,1))!="N") {');
          $code_writer->writePHP('preg_match("~^(.{0,'.$this->len.'}\w*)\b~ims", substr('.$this->str.','.$this->start.'), '.$this->match.');');
          $code_writer->writePHP('}else{');
          $code_writer->writePHP('preg_match("~^(.{0,'.$this->len.'})~ims", substr('.$this->str.','.$this->start.'), '.$this->match.');}');
          $code_writer->writePHP($this->str.'='.$this->match.'[1];');
          $code_writer->writePHP($this->strlen.'=strlen('.$this->str.');');
          $code_writer->writePHP($this->suffix.'=('.$this->strlen.'>='.$this->len.')?');
          $this->parameters[2]->generateExpression($code_writer);
          $code_writer->writePHP(':"";');
          break;
        default:
          //okay
      }
    }

    /**
     * Generate the code to read the data value at run time
     * Must generate only a valid PHP Expression.
     * @param WactCodeWriter
     * @return void
     * @access protected
     */
    function generateExpression($code_writer) {
      switch (count($this->parameters)) {
        case 1:
          $code_writer->writePHP('substr(');
          $this->base->generateExpression($code_writer);
          $code_writer->writePHP(',0,');
          $this->parameters[0]->generateExpression($code_writer);
          $code_writer->writePHP(')');
          break;
        case 2:
          $code_writer->writePHP('substr(');
          $this->base->generateExpression($code_writer);
          $code_writer->writePHP(',');
          $this->parameters[1]->generateExpression($code_writer);
          $code_writer->writePHP(',');
          $this->parameters[0]->generateExpression($code_writer);
          $code_writer->writePHP(')');
          break;
        case 3:
          $code_writer->writePHP('substr('.$this->str.','.$this->start.','.$this->len.').'.$this->suffix);
          break;
        case 4:
          $code_writer->writePHP($this->str.'.'.$this->suffix);
          break;
        default:
          //error
      }
    }

}


