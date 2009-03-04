<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/compiler/filter/WactFilterInfoExtractor.class.php');
require_once('limb/wact/src/compiler/filter/WactFilterInfo.class.php');
require_once('limb/wact/src/compiler/WactCompilerArtifactDictionary.class.php');

/**
 * class WactFilterDictionary.
 *
 * @package wact
 * @version $Id: WactFilterDictionary.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactFilterDictionary extends WactCompilerArtifactDictionary
{
  protected $filter_information = array();

  function _createArtifactsExtractor($file)
  {
    return new WactFilterInfoExtractor($this, $file);
  }

  function registerFilterInfo($filter_info, $file)
  {
    $filter_to_lower = strtolower($filter_info->Name);

    if(isset($this->filter_information[$filter_to_lower]))
      return;

    $filter_info->File = $file;
    $this->filter_information[$filter_to_lower] = $filter_info;
  }

  function getFilterInfo($name)
  {
    $name = strtolower($name);
    if(isset($this->filter_information[$name]))
      return $this->filter_information[$name];
  }
}

