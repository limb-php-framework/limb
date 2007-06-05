<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_app/src/command/lmbActionCommand.class.php');

class lmbCmsSaveObjectPriorityCommand extends lmbActionCommand
{
  function __construct($class_name)
  {
    $this->class_name = $class_name;
    parent :: __construct();
  }

  function perform()
  {
    $priority = $this->request->get('priority');

    if(!is_array($priority) || !sizeof($priority))
      throw new lmbException('"priority" request param should be an array!');

    foreach($priority as $id => $value)
    {
      $item = new $this->class_name((int)$id);
      $item->setPriority($value);
      $item->save();
    }

    $this->closePopup();
  }
}

?>
