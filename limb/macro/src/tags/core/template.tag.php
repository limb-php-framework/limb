<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @tag template
 * @req_attributes name
 * @package macro
 * @version $Id$
 */
class lmbMacroTemplateTag extends lmbMacroPassiveTag
{
  protected $method;
  protected $current_apply_tag = null;
  
  function preParse($compiler)
  {
    if($this->has('name'))
      $this->set('id', 'template_' . $this->get('name'));

    parent :: preParse($compiler);
  }
  
  function generateNow($code, $wrap_with_method = true)
  {
    if($wrap_with_method)
    {
      $args = $code->generateVar();
      $this->method = '_template' . self::generateUniqueId();
      $code->beginMethod($this->getMethod(), array($args . '= array()'));
      $code->writePHP("if($args) extract($args);");
      parent :: generateNow($code);
      $code->endMethod();
    }
    else
      parent :: generateNow($code);
  }

  function generateFromDynamicAppply($code)
  {
    $this->generateNow($code, $wrap_with_method = true);

    $code->writeToInit('if(!isset($this->__template_tags)) $this->__template_tags = array();');
    $code->writeToInit("\n");
    $code->writeToInit('$this->__template_tags["'. $this->get('name') . '"] = "' . $this->getMethod() . '";');
    $code->writeToInit("\n");
  }

  function setCurrentApplyTag(lmbMacroApplyTag $apply_tag)
  {
    $this->current_apply_tag = $apply_tag;
  }
  
  function getCurrentApplyTag()
  {
    return $this->current_apply_tag;
  }
  
  function getMethod()
  {
    return $this->method;
  }
}

