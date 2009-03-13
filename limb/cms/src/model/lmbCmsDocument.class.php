<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/cms/src/model/lmbActiveRecordTreeNode.class.php');

/**
 * class lmbCmsDocument.
 *
 * @package cms
 * @version $Id$
 */
class lmbCmsDocument extends lmbActiveRecordTreeNode
{
  protected $_db_table_name = 'lmb_cms_document';
  protected $_lazy_attributes = array('content');
  protected $_is_being_destroyed = false;
  /**
   * @var lmbMPTree
   */
  protected $_tree;

  function _createValidator()
  {
    $validator = new lmbValidator();

    $validator->addRequiredRule('title', 'Поле "Заголовок" обязательно для заполнения');
    $validator->addRequiredRule('content', 'Поле "Текст" обязательно для заполнения');
    $validator->addRequiredRule('identifier', 'Поле "Идентификатор" обязательно для заполнения');

    lmb_require('limb/cms/src/validation/rule/lmbTreeIdentifierRule.class.php');
    $validator->addRule(new lmbTreeIdentifierRule('identifier'));

    return $validator;
  }

  protected function _onBeforeSave()
  {
    $this->save();
  }

  function _onCreate()
  {
    $this->_setPriority();
  }

  protected function _setPriority()
  {
    if(!$parent_id = $this->getParentId())
      $parent_id = lmbCmsDocument :: findRoot()->getId();

    $sql = "SELECT MAX(priority) FROM " . $this->_db_table_name . " WHERE parent_id = " . $parent_id;
    $max_priority = lmbDBAL :: fetchOneValue($sql);
    $this->setPriority($max_priority + 10);
  }

  function getUri()
  {
    $uri = ($this->getParent() && !$this->getParent()->isRoot()) ? $this->getParent()->getUri() : '';
    return  $uri . '/' . $this->identifier;
  }

  /**
   * @param string $uri
   * @return lmbCmsDocument
   */
  static function findByUri($uri)
  {
    $criteria = lmbSQLCriteria::equal('uri', $uri);
    return lmbActiveRecord :: findFirst('lmbCmsDocument', $criteria);
  }
}


