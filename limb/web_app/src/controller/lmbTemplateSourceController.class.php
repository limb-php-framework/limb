<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    $package$
 */

lmb_require('limb/web_app/src/controller/lmbController.class.php');

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
