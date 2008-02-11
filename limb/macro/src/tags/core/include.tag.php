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
 * @package macro
 * @version $Id$
 */
class lmbMacroIncludeTag extends lmbMacroTag
{
  function preParse($compiler)
  {
    parent :: preParse($compiler);

    if(!$this->isDynamicInclude())
    {
      $compiler->parseTemplate($this->get('file'), $this);
    }
  }

  function isDynamicInclude()
  {
    return $this->isDynamic('file');
  }

  protected function _generateContent($code)
  {
    if($this->isDynamicInclude())
      $this->_generateDynamicaly($code);
    else
      $this->_generateStaticaly($code);
  }

  function _generateDynamicaly($code)
  {
    $handlers_str = 'array(';
    $methods = array();

    //collecting {{into}} tags
    if($intos = $this->_collectIntos())
    {
      foreach($intos as $into)
      {
        $args = $code->generateVar(); 
        $methods[$into->get('slot')] = $code->beginMethod('__slotHandler'. uniqid(), array($args . '= array()'));
        $code->writePHP("if($args) extract($args);"); 
        $into->generateNow($code);
        $code->endMethod();
      }
    }

    foreach($methods as $slot => $method)
      $handlers_str .= '"' . $slot . '"' . ' => array($this, "' . $method . '"),';

    $handlers_str .= ')';

    $arg_str = $this->attributesIntoArrayString();

    $code->writePHP('$this->includeTemplate(' . $this->get('file') . ', ' . $arg_str . ','. $handlers_str . ');');
  }
  
  protected function _collectIntos()
  {
    return $this->findChildrenByClass('lmbMacroIncludeIntoTag');
  }

  function _generateStaticaly($code)
  {
    if($this->getBool('inline'))
      parent :: _generateContent($code);
    else
    {
      static $counter = 1;
  
      list($keys, $vals) = $this->attributesIntoArgs();
  
      $method = $code->beginMethod('__staticInclude' . ($counter++), $keys);
      parent :: _generateContent($code);
      $code->endMethod();
  
      $code->writePHP('$this->' . $method . '(' . implode(', ', $vals) . ');');
    }
  }
}

