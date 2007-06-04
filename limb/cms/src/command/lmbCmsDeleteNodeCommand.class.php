<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCmsDeleteNodeCommand.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/web_app/src/command/lmbFormCommand.class.php');
lmb_require('limb/cms/src/model/lmbCmsNode.class.php');

class lmbCmsDeleteNodeCommand extends lmbFormCommand
{
  function __construct()
  {
    parent :: __construct('', 'delete_form');
  }

  function _onValid()
  {
    if($this->request->get('delete'))
    {
      foreach($this->request->getArray('ids') as $id)
      {
        $node = lmbActiveRecord :: findById('lmbCmsNode', (int)$id);
        $node->destroy();
      }
      $this->closePopup();
    }
  }
}

?>
