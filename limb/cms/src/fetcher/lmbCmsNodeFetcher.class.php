<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCmsNodeFetcher.class.php 4989 2007-02-08 15:35:27Z pachanga $
 * @package    cms
 */
lmb_require('limb/web_app/src/fetcher/lmbFetcher.class.php');
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLRawCriteria.class.php');
lmb_require('limb/cms/src/model/lmbCmsClassName.class.php');
lmb_require('limb/cms/src/model/lmbCmsNode.class.php');

class lmbCmsNodeFetcher extends lmbFetcher
{
  protected $id;
  protected $path;

  function setNodeId($id)
  {
    if(!$id)
      return;

    $this->id = $id;
  }

  function setPath($path)
  {
    $this->path = $path;
  }

  function _createDataSet()
  {
    $toolkit = lmbToolkit :: instance();

    if($this->path && !$this->id)
    {
      $path = $this->path;

      if($node = lmbCmsNode :: findByPath('lmbCmsNode', $path))
        $this->id = $node->id;
    }

    if(!$this->id && $id = $toolkit->getRequest()->getInteger('id'))
      $this->id = $id;

    if($this->id)
      $result = lmbActiveRecord :: find('lmbCmsNode', 'id = ' . $this->id);
    else
    {
      $kids = lmbActiveRecord :: find('lmbCmsNode', "parent_id = 0");
      $result = new lmbArrayDataset(array(array('parent_id' => 0,
                                                'kids' => $kids)));
    }

    return $result;
  }
}

?>
