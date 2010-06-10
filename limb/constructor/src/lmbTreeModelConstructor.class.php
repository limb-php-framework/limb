<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/constructor/src/lmbModelConstructor.class.php');

class lmbTreeModelConstructor extends lmbModelConstructor
{
  protected $_model_template_file = 'model/tree_model.phtml';
  protected $_test_template_file = 'model/tree_test.phtml';


  public function create()
  {
    $vars = array('model_url' => lmb_under_scores($this->_model_name));
    $this->_createRootElementIfNotExists();
    parent :: create($vars);
  }

  protected function _createRootElementIfNotExists()
  {

    if(!$root = lmbDBAL :: fetchOneRow('SELECT * FROM ' . $this->_table->getName()))
      lmbDBAL :: execute('INSERT INTO ' . $this->_table->getName() . ' (parent_id, level, priority, is_published, path, identifier, ctime, utime) VALUES (0,0,0,0,\'/1/\',\'\',0,0)');
  }
}



