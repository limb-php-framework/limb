<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactTemplateConfig.interface.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */


interface WactTemplateConfig
{
  function isForceScan();
  function isForceCompile();
  function getCacheDir();
  function getScanDirectories();
  function getSaxFilters();
}

?>