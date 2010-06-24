<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
/**
 * @filter i18n_clip
 * @min_attributes 1
 * @max_attributes 4
 * @package i18n
 * @version $Id: clip.filter.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class I18NClipFilter extends WactCompilerFilter
{
  var $str;
  var $strlen;
  var $start;
  var $len;
  var $suffix;
  var $match;

  function getValue()
  {
    $suffix = '';

    if ($this->isConstant())
    {
      $value = $this->base->getValue();
      switch (count($this->parameters)) {
      case 1:
        return lmb_substr($value, 0, $this->parameters[0]->getValue());
        break;
      case 2:
        return lmb_substr($value, $this->parameters[1]->getValue(), $this->parameters[0]->getValue());
        break;
      case 3:
        $suffix = $this->_getSuffix($value,
                                   $this->parameters[0]->getValue(),
                                   $this->parameters[1]->getValue(),
                                   $this->parameters[2]->getValue());
        return lmb_substr($value, $this->parameters[1]->getValue(), $this->parameters[0]->getValue()) . $suffix;
        break;
      case 4:
        $limit = $this->parameters[0]->getValue();
        $offset = $this->parameters[1]->getValue();
        $word_wrap = $this->parameters[3]->getValue();
        $suffix = $this->_getSuffix($value, $limit, $offset, $this->parameters[2]->getValue());

        if (strtoupper(substr($word_wrap,0,1)) != 'N')
        {
            if(lmb_preg_match('~^(.{0,'.$limit.'}(?U)\w*)\b~ism', lmb_substr($value, $offset), $match))
              return $match[1].$suffix;
            else
              return '';
        }
        else
          return lmb_substr($value, $offset, $limit) . $suffix;
        break;
      default:
          throw new WactException('Wrong number of filter parameters(1..4)');
      }
    }
    else
      $this->raiseUnresolvedBindingError();
  }

  function _getSuffix($value, $limit, $offset, $suffix)
  {
    $result = '';
    if(lmb_strlen($value) > ($limit + $offset))
      $result = $suffix;
    return $result;
  }

  function generatePreStatement($code)
  {
    parent::generatePreStatement($code);

    switch (count($this->parameters))
    {
      case 1:
        $this->_generateBaseVars($code);
        break;
      case 2:
        $this->_generateBaseVars($code);
        $this->_generateOffset($code);
        break;
      case 3:
      case 4:
        $this->_generateBaseVars($code);
        $this->_generateOffset($code);
        $this->_generateSuffix($code);
        break;
      default:
        throw new WactException('Wrong number of filter parameters(1..4)');
    }
  }

  function generateExpression($code)
  {
    switch (count($this->parameters))
    {
      case 1:
        $code->writePHP('lmb_substr('.$this->str.',0 ,'.$this->len.')');
        break;
      case 2:
        $code->writePHP('lmb_substr('.$this->str.','.$this->start.','.$this->len.')');
        break;
      case 3:
      case 4:
          $code->writePHP('lmb_substr('.$this->str.','.$this->start.','.$this->len.').' . $this->suffix);
          break;
      default:
        throw new WactException('Wrong number of filter parameters(1..4)');
    }
  }

  protected function _generateBaseVars($code)
  {
    $this->str = $code->getTempVarRef();
    $this->strlen = $code->getTempVarRef();
    $this->len = $code->getTempVarRef();

    $code->writePHP($this->str.'=');
    $this->base->generateExpression($code);
    $code->writePHP(';');

    $code->writePHP($this->strlen.' = lmb_strlen('.$this->str.');');

    $code->writePHP($this->len.'=');
    $this->parameters[0]->generateExpression($code);
    $code->writePHP(';');
  }

  protected function _generateOffset($code)
  {

    $this->start = $code->getTempVarRef();


    $code->writePHP($this->start.'=');
    $this->parameters[1]->generateExpression($code);
    $code->writePHP(';');

  }

  protected function _generateSuffix($code)
  {
    $this->suffix = $code->getTempVarRef();

    $code->writePHP($this->suffix.'=('.$this->strlen.'>'.$this->start.'+'.$this->len.')?');
    $this->parameters[2]->generateExpression($code);
    $code->writePHP(':\'\';');
  }
}

