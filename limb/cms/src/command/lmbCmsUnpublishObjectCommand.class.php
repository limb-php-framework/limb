<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCmsUnpublishObjectCommand.class.php 4989 2007-02-08 15:35:27Z pachanga $
 * @package    cms
 */
lmb_require('limb/web_app/src/command/lmbActionCommand.class.php');

class lmbCmsUnpublishObjectCommand extends lmbActionCommand
{
  protected $class_name;

  function __construct($class_name)
  {
    $this->class_name = $class_name;
    parent :: __construct();
  }

  function perform()
  {
    $ids = $this->request->get('ids');

    if(!is_array($ids) || !count($ids))
      $this->closePopup();

    foreach($ids as $id)
    {
      $item = new $this->class_name((int)$id);
      $item->setIsPublished(false);
      $item->save();
    }

    $this->closePopup();
  }
}

?>
