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
 * abstract class AdminObjectController.
 *
 * @package cms
 * @version $Id$
 */
abstract class lmbObjectController extends lmbController
{
  protected $_object_class_name = '';

  function __construct()
  {
    parent :: __construct();

    if(!$this->_object_class_name)
      throw new lmbException('Object class name is not specified');
  }

  /**
   * @return lmbActiveRecord
   */
  protected function _getObjectByRequestedId($throw_exception = false)
  {
    if(!$id = $this->request->getInteger('id'))
      return false;

    if(!$item = lmbActiveRecord::findById($this->_object_class_name, $id, $throw_exception))
      return false;

    return $item;
  }

  function doDisplay()
  {
    $this->items = lmbActiveRecord::find($this->_object_class_name);
  }

  function doItem()
  {
    if(!$this->item = $this->_getObjectByRequestedId())
      return $this->forwardTo404();
  }
}


