<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/tags/form/form.tag.php');
/**
 * This tag allows you to have several forms on a single page and to submit them on a single page
 * In this case your request will have a variable named after submitted form
 * with all form elements inside
 * @tag form_multiple
 * @suppress_attributes children_reuse_runat
 * @restrict_self_nesting
 * @package wact
 * @version $Id: form_multiple.tag.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactFormMultipleTag extends WactFormTag
{
  protected $runtimeComponentName = 'WactMultipleFormComponent';
  protected $runtimeIncludeFile = 'limb/wact/src/components/form/WactMultipleFormComponent.class.php';

  function prepare()
  {
    parent :: prepare();

    if(!$form_name = $this->getAttribute('name'))//should we leave it like that?
      return;

    $this->_renameChildren($form_name, $this->children);

    $this->tag = 'form';
  }

  function _renameChildren($form_name, $children)
  {
    foreach($children as $child)
    {
      if(is_a($child, 'WactControlTag') && ($name = $child->getAttribute('name')))
      {
        $child->removeAttribute('name');
        $child->setAttribute('name', $form_name . $this->_makeWrappedName($name));
      }

      if(sizeof($child->children) > 0)
        $this->_renameChildren($form_name, $child->children);
    }
  }

  function _makeWrappedName($name)
  {
    return preg_replace('/^([^\[\]]+)(\[.*\])*$/', "[\\1]\\2", $name);
  }

}

