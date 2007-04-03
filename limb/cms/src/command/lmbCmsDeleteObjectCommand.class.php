<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCmsDeleteObjectCommand.class.php 5166 2007-02-28 14:10:28Z tony $
 * @package    cms
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
