<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_app/src/command/lmbFormCommand.class.php');

/**
 * class lmbCmsDeleteObjectCommand.
 *
 * @package cms
 * @version $Id: lmbCmsDeleteObjectCommand.class.php 6221 2007-08-07 07:24:35Z pachanga $
 */
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


