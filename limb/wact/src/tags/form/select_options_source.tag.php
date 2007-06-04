<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: select_options_source.tag.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

/**
* @tag select:OPTIONS_SOURCE
* @known_parent WactFormTag
* @forbid_end_tag
* @req_const_attributes target
* @convert_to_expression from
*/
class WactSelectOptionsSource extends WactRuntimeDatasourceComponentTag
{
  protected $runtimeComponentName = 'WactSelectOptionsSourceComponent';
  protected $runtimeIncludeFile = 'limb/wact/src/components/form/WactSelectOptionsSourceComponent.class.php';

  function generateTagContent($code)
  {
    $targets = $this->_getTargetsAsArray();
    if(!$this->_isTargetsOk($targets))
      return;

    if($value = $this->getAttribute('use_as_name'))
      $code->writePHP($this->getComponentRefCode() . '->useAsName("' . $value . '");');

    if($value = $this->getAttribute('use_as_id'))
      $code->writePHP($this->getComponentRefCode() . '->useAsId("' . $value . '");');

    if($value = $this->getAttribute('default_value'))
      $code->writePHP($this->getComponentRefCode() . '->setDefaultValue("' . $value . '");');

    if($value = $this->getAttribute('default_name'))
      $code->writePHP($this->getComponentRefCode() . '->setDefaultName("' . $value . '");');

    foreach($targets as $target)
    {
      $target_tag = $this->parent->findChild($target);
      $code->writePhp($target_tag->getComponentRefCode() . '->setChoices(' . $this->getComponentRefCode() .'->getChoices());') . "\n";
    }
  }

  function _isTargetsOk($targets)
  {
    foreach($targets as $target)
    {
      $target_tag = $this->parent->getChild($target);

      if(!is_a($target_tag, 'WactSelectTag'))
      {
        $this->raiseCompilerError('Select tag not found',
                                  array('id' => $target));
        return false;
      }
    }
    return true;
  }

  function _getTargetsAsArray()
  {
    $targets = $this->getAttribute('target');

    $result = array();

    $pieces = explode(',', $targets);
    foreach($pieces as $piece)
      $result[] = trim($piece);

    return $result;
  }
}
?>
