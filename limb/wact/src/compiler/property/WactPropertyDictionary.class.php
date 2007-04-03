<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactPropertyDictionary.class.php 5142 2007-02-20 15:44:30Z wiliam $
 * @package    wact
 */

require_once 'limb/wact/src/compiler/property/WactPropertyInfo.class.php';
require_once 'limb/wact/src/compiler/WactCompilerArtifactDictionary.class.php';
require_once 'limb/wact/src/compiler/property/WactPropertyInfoExtractor.class.php';

class WactPropertyDictionary extends WactCompilerArtifactDictionary
{
  protected $property_information = array();

  function _createArtifactsExtractor($file)
  {
    return new WactPropertyInfoExtractor($this, $file);
  }

  function registerPropertyInfo($PropertyInfo, $file)
  {
    $name_to_lower = strtolower($PropertyInfo->Property);
    $tag_class = $PropertyInfo->TagClass;

    if(isset($this->property_information[$tag_class]) && isset($this->property_information[$tag_class][$name_to_lower]))
      return;

    if(!isset($this->property_information[$tag_class]))
      $this->property_information[$tag_class] = array();

    $PropertyInfo->File = $file;
    $this->property_information[$tag_class][$name_to_lower] = $PropertyInfo;
  }

  function getPropertyList($tag)
  {
    $result = array();

    foreach($this->property_information as $tag_class => $props)
    {
      if(!is_a($tag, $tag_class))
        continue;

      foreach($props as $name => $property)
        $result[$name] = $property;
    }

    return $result;
  }
}
?>