<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/constructor/src/lmbAdminControllertConstructor.class.php');

class lmbAdminTreeControllerConstructor extends lmbAdminControllerConstructor
{
  function create()
  {
    $vars = array(
      'model_name' => $this->_model_name,
      'datetime_columns' => $this->_findDateTimeColumns(),
    );
    $content = $this->_createContentFromTemplate('admin_controller/tree_controller.phtml', $vars);
    $this->_project->addController($this->getControllerFileName(), $content);
  }
}

