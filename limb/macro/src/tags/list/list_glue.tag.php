<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Compile time component for separators in a list
 * @tag list:glue
 * @aliases list:separator 
 * @parent_tag_class lmbMacroListItemTag
 * @package macro
 * @version $Id$
 */
class lmbMacroListGlueTag extends lmbMacroTag
{
  protected $step_var;
  protected $helper_var;
  protected $pregenerated_attributes = false;

  function preParse($compiler)
  {
    $list = $this->findParentByClass('lmbMacroListTag');
    $list->countSource();
    
    if($this->has('every'))
      $this->set('step', $this->get('every'));
  }
  
  function _preGenerateAttributes($code_writer)
  {
    if($this->pregenerated_attributes)
      return;
    
    $this->pregenerated_attributes = true;
    parent :: _preGenerateAttributes($code_writer);
  }
  
  // called by parent {{list}} tag (lmbMacroListTag)
  function generateInitCode($code)
  {
    $this->_preGenerateAttributes($code);
    
    $step_var = $this->getStepVar($code);
    $helper_var = $this->getHelperVar($code);
    
    $code->writePHP("if(!isset({$helper_var})){\n");
    $code->registerInclude('limb/macro/src/tags/list/lmbMacroListGlueHelper.class.php');
    $code->writePHP($helper_var . " = new lmbMacroListGlueHelper();\n");
    $code->writePHP("}\n");

    if($this->has('step'))
      $code->writePHP($step_var . " = " . $this->get('step') . ";\n");
    else
      $code->writePHP($step_var . " = 1;\n");

    $code->writePhp($helper_var . "->setStep({$step_var});\n");
    $list = $this->findParentByClass('lmbMacroListTag');
    $source_var = $list->getSourceVar();
    $code->writePhp($helper_var . "->setTotalItems(count($source_var));\n");
  }

  protected function _generateContent($code)
  {
    $step_var = $this->getStepVar($code);
    $helper_var = $this->getHelperVar($code);
    
    $code->writePhp($helper_var . "->next();\n");

    $code->writePhp("if ( " . $helper_var  . "->shouldDisplay()){\n");

    $code->writePhp($helper_var . "->reset();\n");

    $separators = $this->parent->findImmediateChildrenByClass('lmbMacroListGlueTag');
    if(array($separators) && count($separators))
    {
      foreach($separators as $separator)
      {
        if(!$separator->isIndependent() && $separator->getNodeId() != $this->getNodeId())
        {
          $code->writePhp('if (' . $separator->getStepVar($code) . ' < ' . $step_var . ') ');
          $code->writePhp($separator->getHelperVar($code) . "->skipNext();\n");
        }
      }
    }

    parent :: _generateContent($code);

    $code->writePhp("}\n");
  }
  
  function isIndependent()
  {
    return $this->has('independent') && $this->getBool('independent');
  }

  function getStepVar($code)
  {
    if(!$this->step_var)
      $this->step_var = $code->generateVar();

    return $this->step_var;
  }

  function getHelperVar($code)
  {
    if(!$this->helper_var)
      $this->helper_var = $code->generateVar();
    return $this->helper_var;
  }
}

