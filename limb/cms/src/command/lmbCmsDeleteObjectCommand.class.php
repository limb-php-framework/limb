<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCmsDeleteObjectCommand.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/web_app/src/command/lmbFormCommand.class.php');

class lmbCmsDeleteObjectCommand extends lmbFormCommand
{
  protected $class_name;

  function __construct($class_name)
  {
    $this->class_name = $class_name;

    parent :: __construct('', 'delete_form');
  }

  function _onValid()
  {
    if($this->request->get('delete'))
    {
      foreach($this->request->getArray('ids') as $id)
      {
        $item = new $this->class_name((int)$id);
        $item->destroy();
      }
      $this->closePopup();
    }
  }
}

?>
