<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

//temporary includes, make it more flexible later
lmb_require('limb/macro/src/lmbMacroTagDictionary.class.php');
lmb_require('limb/macro/src/lmbMacroTagInfo.class.php');
lmb_require('limb/macro/src/lmbMacroTag.class.php');

lmbMacroTagDictionary :: instance()->register(new lmbMacroTagInfo('include', 'lmbMacroIncludeTag', false), __FILE__);

/**
 * class lmbMacroIncludeTag.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroIncludeTag extends lmbMacroTag
{
  function preParse($compiler)
  {
    parent :: preParse($compiler);

    $locator = $compiler->getTemplateLocator();

    if(!$file = $this->get('file'))
      $this->raiseRequiredAttributeError($file);

    if(!$this->_isDynamic())
    {
      $source_file = $locator->locateSourceTemplate($file);
      if(empty($source_file))
        $this->raise('Template source file not found', array('file_name' => $file));

      $compiler->parseTemplate($file, $this);
    }
  }

  function _isDynamic()
  {
    return $this->isVariable('file');
  }

  function generateContents($code)
  {
    if($this->_isDynamic())
      $this->_generateDynamicContents($code);
    else
      $this->_generateStaticContents($code);
  }

  function _generateDynamicContents($code)
  {
    $args = $this->_attributesIntoArray();

    $arg_str = 'array(';
    foreach($args as $key => $value)
      $arg_str .= "'$key' => $value,";
    $arg_str .= ')';

    $code->writePHP('$this->includeTemplate(' . $this->get('file') . ',' . $arg_str . ');');
  }

  function _generateStaticContents($code)
  {
    static $counter = 1;

    list($keys, $vals) = $this->_attributesIntoArgs();

    $method = $code->beginMethod('__staticInclude' . ($counter++), $keys);
    parent :: generateContents($code);
    $code->endMethod();

    $code->writePHP('$this->' . $method . '(' . implode(', ', $vals) . ');');
  }

  protected function _attributesIntoArgs()
  {
    $keys = array();
    $vals = array();
    foreach($this->attributes as $k => $v)
    {
      $keys[] = '$' . $k;
      $vals[] = $this->_sanitizeValue($v);
    }
    return array($keys, $vals);
  }

  protected function _attributesIntoArray()
  {
    $arr = array();
    foreach($this->attributes as $k => $v)
      $arr[$k] = $this->_sanitizeValue($v);
    return $arr;
  }

  protected function _sanitizeValue($v)
  {
    if(is_numeric($v) || $v{0} == '$')
      return $v;
    else //make it smarter
      return "'$v'";
  }
}

