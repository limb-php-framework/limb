<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCmsNodeKidsFetcher.class.php 5729 2007-04-20 12:32:19Z pachanga $
 * @package    cms
 */

lmb_require('limb/web_app/src/fetcher/lmbFetcher.class.php');
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLRawCriteria.class.php');
lmb_require('limb/cms/src/model/lmbCmsClassName.class.php');

class lmbCmsNodeKidsFetcher extends lmbFetcher
{
  protected $controller_name;
  protected $parent_id = null;
  protected $path;

  function setController($controller_name)
  {
    $this->controller_name = $controller_name;
  }

  function setParentId($parent_id)
  {
    if($parent_id)
      $this->parent_id = $parent_id;
  }

  function setParentPath($path)
  {
    $this->path = $path;
  }

  function _createDataSet()
  {
    $toolkit = lmbToolkit :: instance();

    if($this->path && $this->parent_id === null)
    {
      if($node = lmbCmsNode :: findByPath('lmbCmsNode', $this->path))
        $this->parent_id = $node->id;
    }

    if($this->parent_id === null)
    {
      $tree = $toolkit->getCmsTree();
      if(!$root = $tree->getRootNode())
        return array();
      $this->parent_id = $root->get('id');
    }

    $criteria = new lmbSQLRawCriteria("parent_id = " . (int)$this->parent_id);
    if($this->controller_name)
    {
      $controller_id = lmbCmsClassName :: generateIdFor($this->controller_name);
      $criteria->addAnd(new lmbSQLRawCriteria('controller_id ='. $controller_id));
    }

    return lmbActiveRecord :: find('lmbCmsNode', array('criteria' => $criteria));
  }
}

?>
