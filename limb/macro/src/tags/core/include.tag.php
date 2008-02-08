<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbMacroIncludeTag.
 *
 * @tag include
 * @req_attributes file
 * @forbid_end_tag   
 * @package macro
 * @version $Id$
 */
class lmbMacroIncludeTag extends lmbMacroTag
{
  function preParse($compiler)
  {
    parent :: preParse($compiler);

    if(!$this->_isDynamic())
    {
      $compiler->parseTemplate($this->get('file'), $this);
    }
  }

  function _isDynamic()
  {
    return $this->isDynamic('file');
  }

  protected function _generateContent($code)
  {
    if($this->_isDynamic())
      $this->_generateDynamicaly($code);
    else
      $this->_generateStaticaly($code);
  }

  function _generateDynamicaly($code)
  {
    $arg_str = $this->attributesIntoArrayString();

    $code->writePHP('$this->includeTemplate(' . $this->get('file') . ',' . $arg_str . ');');
  }

  function _generateStaticaly($code)
  {
    static $counter = 1;

    list($keys, $vals) = $this->attributesIntoArgs();

    $method = $code->beginMethod('__staticInclude' . ($counter++), $keys);
    parent :: _generateContent($code);
    $code->endMethod();

    $code->writePHP('$this->' . $method . '(' . implode(', ', $vals) . ');');
  }
}

