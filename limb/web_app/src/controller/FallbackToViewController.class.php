<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */ 
lmb_require('limb/web_app/src/controller/lmbAbstractController.class.php');

/**
 * class FallbackToViewController.
 * To use this controller just pass it's name to lmbRequestDispatchingFilter, e.g.:
 *  <code>
 *   $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbRequestDispatchingFilter',
 *                                       array(new lmbHandle('limb/web_app/src/request/lmbRoutesRequestDispatcher'),
 *                                            'any_template')));
 *  </code> 
 *
 * @package web_app
 * @version $Id: lmbController.class.php 6497 2007-11-07 13:27:32Z serega $
 */ 
class FallbackToViewController extends lmbAbstractController
{
  function performAction()
  {
    $toolkit = lmbToolkit :: instance();
    $path = trim($toolkit->getRequest()->getUriPath(), '/');
    
    if($template_path = $this->_findTemplateByFormat($path . '%s'))
    {      
      $view = $this->toolkit->createViewByTemplate($template_path);
      $this->toolkit->setView($view);
    }
    else
      return $this->forwardTo404();
  }
  
  function actionExists($action)
  {
    return true;
  }
}