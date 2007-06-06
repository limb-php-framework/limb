<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/tree/src/lmbMPTree.class.php');

/**
 * class lmbCmsTools.
 *
 * @package cms
 * @version $Id: lmbCmsTools.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class lmbCmsTools extends lmbAbstractTools
{
  protected $tree;

  function getCmsTree()
  {
    if(is_object($this->tree))
      return $this->tree;

    $this->tree = new lmbMPTree('node');

    return $this->tree;
  }

  function setCmsTree($tree)
  {
    $this->tree = $tree;
  }
}

?>
