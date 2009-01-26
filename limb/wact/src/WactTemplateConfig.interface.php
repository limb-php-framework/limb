<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * interface WactTemplateConfig.
 *
 * @package wact
 * @version $Id: WactTemplateConfig.interface.php 7486 2009-01-26 19:13:20Z pachanga $
 */
interface WactTemplateConfig
{
  function isForceScan();
  function isForceCompile();
  function getCacheDir();
  function getScanDirectories();
  function getSaxFilters();
}


