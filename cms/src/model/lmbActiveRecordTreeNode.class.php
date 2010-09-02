<?php

lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/tree/src/lmbMPTree.class.php');

abstract class lmbActiveRecordTreeNode extends lmbActiveRecord
{
  protected $_tree;

  function __construct($magic_params = null, $conn = null)
  {
    parent :: __construct($magic_params, $conn);

    $this->_tree = $this->getTree();
  }


  /**
   * @return lmbMPTree
   */
  function getTree()
  {
    if(!$this->_tree)
      $this->_tree = lmbToolkit :: instance()->getCmsTree($this->getTableName(), $this->_db_conn);

    return $this->_tree;
  }

  function loadByPath($path)
  {
    if(!$node = $this->getTree()->getNodeByPath($path));
      throw new lmbARException('Could not found element by path ' . $path);

    return $this->import($node);
  }

  function _defineRelations()
  {
    $this->_has_one = array('parent' => array('field' => 'parent_id',
                                               'class' => get_class($this),
                                               'can_be_null' => true,
                                               'cascade_delete' => false));

    $this->_has_many = array('kids' => array('field' => 'parent_id',
                                             'class' => get_class($this)));
  }

  protected function _insertDbRecord($values)
  {
    if($this->getParent() && $parent_id = $this->getParent()->getId())
      return $this->_tree->createNode($parent_id, $values);
    else
    {
      if(!$root = $this->_tree->getRootNode())
      {
        $this->_tree->initTree();
        $root = $this->_tree->getRootNode();
      }

      return $this->_tree->createNode($root, $values);
    }
  }

  protected function _updateDbRecord($values)
  {
    return $this->getTree()->updateNode($this->getId(), $values);
  }

  protected function _onAfterUpdate()
  {
    if($this->isDirtyProperty('parent'))
    {
      $this->getTree()->moveNode($this->getId(), $this->getParent()->getId());
    }
  }

  protected function _deleteDbRecord()
  {
    $this->getTree()->deleteNode($this->getId());
  }

  /**
   * @param integer $depth
   * @return lmbCollection
   */
  function getChildren($depth = 1)
  {
    return lmbActiveRecord :: decorateRecordSet($this->getTree()->getChildren($this->getId(), $depth),
                                                get_class($this),
                                                $this->_db_conn);
  }

  function getChildrenAll()
  {
    return lmbActiveRecord :: decorateRecordSet($this->getTree()->getChildrenAll($this->getId()),
                                                get_class($this),
                                                $this->_db_conn);
  }

  function move($target)
  {
    $this->getTree()->moveNode($this->getId(), $target);
  }

  static function findRoot($class_name = '')
  {
    if(!$class_name)
      $class_name = self :: _getCallingClass();

    return lmbActiveRecord :: findOne($class_name, lmbSQLCriteria::equal('parent_id', 0));
  }

  /**
   * @return bool
   */
  function isRoot()
  {
    if($this->isNew()) return false;
    return !((bool)$this->_getRaw('parent_id'));
  }

  
  /**
   *
   * @param lmbActiveRecordTreeNode $node
   * @return bool
   */
  function isChildOf( lmbActiveRecordTreeNode $node)
  {
    $rs = $this->getTree()->getParents($this);
    foreach($rs as $record)
    {
      if((int)$record['id']===(int)$node['id']) return true;
    }
    return false;
  }

  function getParents()
  {
    $rs = $this->getTree()->getParents($this);
    return lmbActiveRecord :: decorateRecordSet($rs,
                                                get_class($this),
                                                $this->_db_conn);
  }
}
