<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/web_app/src/controller/lmbController.class.php');

/**
 * class lmbTemplateSourceController.
 *
 * @package web_app
 * @version $Id$
 */
class lmbTemplateSourceController extends lmbController
{
  function doDisplay()
  {
    require_once 'limb/wact/src/compiler/templatecompiler.inc.php';
    $this->view->set('this_template_path', $this->view->getTemplate());
    $this->performCommand('limb/web_app/src/command/lmbShowWactTemplateSourceCommand', 'template_source');
  }
}

?>
