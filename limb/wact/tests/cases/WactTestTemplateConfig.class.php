<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactTestTemplateConfig.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

require_once('limb/wact/src/WactTemplateConfig.interface.php');

class WactTestTemplateConfig implements WactTemplateConfig
{
  protected $source;

  function __construct($source)
  {
    $this->source = $source;
  }

  function getCacheDir()
  {
    if(isset($this->source['cache_dir']))
      return $this->source['cache_dir'];
    else
      return WACT_CACHE_DIR;
  }

  function isForceScan()
  {
    if(isset($this->source['forcescan']))
      return $this->source['forcescan'];
    else
      return true;
  }

  function isForceCompile()
  {
    if(isset($this->source['forcecompile']))
      return $this->source['forcecompile'];
    else
      return true;
  }

  function getScanDirectories()
  {
    if(isset($this->source['scan_directories']))
      return $this->source['scan_directories'];
    else
      return array('limb/wact/src/tags/');
  }

  function getSaxFilters()
  {
    if(isset($this->source['saxfilters']))
      return $this->source['saxfilters'];
    else
      return array();
  }

}

?>