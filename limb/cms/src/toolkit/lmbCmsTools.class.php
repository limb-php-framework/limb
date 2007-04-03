<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCmsTools.class.php 4989 2007-02-08 15:35:27Z pachanga $
 * @package    cms
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/tree/src/tree/lmbMaterializedPathTree.class.php');
lmb_require('limb/tree/src/tree/lmbCachingTree.class.php');

class lmbCmsTools extends lmbAbstractTools
{
  protected $tree;

  function getCmsTree()
  {
    if(is_object($this->tree))
      return $this->tree;

    $this->tree = new lmbMaterializedPathTree('node');

    return $this->tree;
  }

  function setCmsTree($tree)
  {
    $this->tree = $tree;
  }
}

?>
