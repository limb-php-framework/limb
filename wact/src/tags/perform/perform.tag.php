<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
/**
 * @tag perform
 * @req_const_attributes command
 * @package wact
 * @version $Id$
 */
class WactPerformTag extends WactRuntimeComponentTag
{
  protected $runtimeComponentName = 'WactPerformComponent';
  protected $runtimeIncludeFile = 'limb/wact/src/components/perform/WactPerformComponent.class.php';

  function generateAfterContent($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->setCommand("' . $this->getAttribute('command') .'");' . "\n");

    if($this->hasAttribute('include'))
    $code->writePhp($this->getComponentRefCode() . '->setIncludePath("' . $this->getAttribute('include') .'");' . "\n");

    if($this->hasAttribute('method'))
      $code->writePhp($this->getComponentRefCode() . '->setMethod("' . $this->getAttribute('method') .'");' . "\n");

    $code->writePhp(' echo ' . $this->getComponentRefCode() . '->process($template);' . "\n");
  }

}


