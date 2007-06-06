<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_app/src/command/lmbFormCommand.class.php');
lmb_require('limb/cms/src/model/lmbCmsNode.class.php');

/**
 * class lmbCmsDeleteNodeCommand.
 *
 * @package cms
 * @version $Id: lmbCmsDeleteNodeCommand.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
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
