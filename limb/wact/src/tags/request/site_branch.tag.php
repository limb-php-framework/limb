<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    web_app
 */
/**
* @tag site_branch
* @parent_tag_class WactSiteBranchSelectorTag
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

?>