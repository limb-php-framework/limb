<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/view/src/lmbView.class.php');

/**
 * class lmbBlitzView.
 *
 * @package view
 * @version $Id$
 */
class lmbBlitzView extends lmbView
{
  private $templateInstance;

  static function locateTemplateByAlias($alias)
  {
    return null;
  }

  function __call($methodName, $params)
  {
    $tpl = $this->getTemplateInstance();
    if(!method_exists($tpl, $methodName))
    {
      throw new lmbException(
          'Wrong template method called', 
          array(
            'template class' => get_class($tpl),
            'method' => $methodName,
            )
          );
    }
    return call_user_method_array($methodName, $tpl, $params);        
  }

  function getTemplateInstance()
  {
    if(!$this->templateInstance)
    {
      if(!class_exists('Blitz'))
        throw new lmbException("Blitz extension is not loaded");

      $this->templateInstance = new Blitz($this->getTemplate());
    }
    return $this->templateInstance;
  }

  function render()
  {
    foreach ($this->getVariables() as $name => $value)
      $this->getTemplateInstance()->set(array($name => $value));

    return $this->getTemplateInstance()->parse();
  }

}
