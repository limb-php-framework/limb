<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/cms/src/model/lmbCmsNode.class.php');

/**
 * class lmbCmsRootNode.
 *
 * @package cms
 * @version $Id$
 */
class lmbCmsRootNode extends lmbCmsNode
{
  protected function _createValidator()
  {
    $validator = new lmbValidator();
    return $validator;
  }

  protected function _insertDbRecord($values)
  {
    $root_id = $this->_tree->initTree();
    $this->_tree->updateNode($root_id, $values);
    return $root_id;
  }

  function getUrlPath()
  {
    return '/';
  }

  function getParents()
  {
    return new lmbCollection();
  }
}


