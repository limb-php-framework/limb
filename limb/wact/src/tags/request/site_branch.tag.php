<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
/**
 * @tag site_branch
 * @parent_tag_class WactSiteBranchSelectorTag
 * @package wact
 * @version $Id$
 */
class WactSiteBranchTag extends WactSilentCompilerTag
{
  protected $path;
  protected $is_default = false;

  function preParse()
  {
    parent :: preParse();

    if($default = $this->getAttribute('default'))
      $this->is_default = true;

    $this->path = $this->getAttribute('path');
    if(!$this->is_default && !$this->path)
    {
      $this->raiseRequiredAttributeError('path or default');
    }
  }

  function isDefault()
  {
    return $this->is_default;
  }

  function getPath()
  {
    return $this->path;
  }
}


