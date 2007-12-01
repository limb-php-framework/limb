<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/lmbMacroTag.class.php');

/**
 * class lmbMacroIncludeTag.
 *
 * @tag include
 * @req_attributes file
 * @package macro
 * @version $Id$
 */
class lmbMacroIncludeTag extends lmbMacroTag
{
  function preParse($compiler)
  {
    parent :: preParse($compiler);

    $locator = $compiler->getTemplateLocator();

    $file = $this->get('file');

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
    return $this->isDynamic('file');
  }

  function generate($code)
  {
    if($this->_isDynamic())
      $this->_generateDynamicaly($code);
    else
      $this->_generateStaticaly($code);
  }

  function _generateDynamicaly($code)
  {
    $args = $this->_attributesIntoArray();

    $arg_str = 'array(';
    foreach($args as $key => $value)
      $arg_str .= "'$key' => $value,";
    $arg_str .= ')';

    $code->writePHP('$this->includeTemplate(' . $this->get('file') . ',' . $arg_str . ');');
  }

  function _generateStaticaly($code)
  {
    static $counter = 1;

    list($keys, $vals) = $this->_attributesIntoArgs();

    $method = $code->beginMethod('__staticInclude' . ($counter++), $keys);
    parent :: generate($code);
    $code->endMethod();

    $code->writePHP('$this->' . $method . '(' . implode(', ', $vals) . ');');
  }

  protected function _attributesIntoArgs()
  {
    $keys = array();
    $vals = array();
    foreach($this->attributes as $k => $attribute)
    {
      $keys[] = '$' . $k;
      $vals[] = $this->getEscaped($k);
    }
    return array($keys, $vals);
  }

  protected function _attributesIntoArray()
  {
    $arr = array();
    foreach($this->attributes as $k => $attribute)
      $arr[$k] = $this->getEscaped($k);
    return $arr;
  }
}

