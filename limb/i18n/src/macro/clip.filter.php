<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Filter i18n_clip for macro templates
 * @filter i18n_clip
 * @package i18n
 * @version $Id$
 */
class lmbI18NMacroClipFilter extends lmbMacroFilter
{
  function preGenerate($code)
  {
    $code->registerInclude('limb/i18n/src/charset/driver.inc.php');
    parent :: preGenerate($code);
  }

  function getValue()
  {
    $suffix = '';

    $value = $this->base->getValue();
    switch (count($this->params)) {
    case 1:
      return 'lmb_substr('. $value .','. 0 .','. $this->params[0] .')';
      break;
    case 2:
      return 'lmb_substr('. $value .','. $this->params[1] .','. $this->params[0]. ')';
      break;
    case 3:
      $suffix = $this->_getSuffix($value,
                                 $this->params[0],
                                 $this->params[1],
                                 $this->params[2]);
        return 'lmb_substr(' . $value .','. $this->params[1] .','. $this->params[0] .')' . $suffix;
      break;
    case 4:
      $limit = $this->params[0];
      $offset = $this->params[1];
      $word_wrap = $this->params[3];
      $suffix = $this->_getSuffix($value, $limit, $offset, $this->params[2]);

      if (strtoupper(substr($word_wrap,0,1)) != 'N')
      {
          if(lmb_preg_match('~^(.{0,'.$limit.'}(?U)\w*)\b~ism', lmb_substr($value, $offset), $match))
            return $match[1].$suffix;
          else
            return '';
      }
      else
        return 'lmb_substr('. $value .','. $offset. ','. $limit .')' . $suffix;
      break;
    default:
        throw new lmbMacroException('Wrong number of filter params(1..4)');
    }
  }

  function _getSuffix($value, $limit, $offset, $suffix)
  {
    $result = '';
    if(lmb_strlen($value) > ($limit + $offset))
      $result = '.' . $suffix;
    return $result;
  }
}