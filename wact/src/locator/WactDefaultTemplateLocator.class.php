<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/locator/WactTemplateLocator.interface.php');

/**
 * class WactDefaultTemplateLocator.
 *
 * @package wact
 * @version $Id: WactDefaultTemplateLocator.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactDefaultTemplateLocator implements WactTemplateLocator
{
  /**
   * @var WactTemplateConfig
   */
  protected $config;

  protected $templates_dir;

  public function __construct($config)
  {
    $this->config = $config;

    if(method_exists($config, 'getTemplatesDir'))
      $this->templates_dir = $this->config->getTemplatesDir();
    else
      $this->templates_dir = 'templates/';
  }

  public function locateCompiledTemplate($file_name)
  {
    return $this->config->getCacheDir() . '/' . md5($file_name) . '.php';
  }

  public function locateSourceTemplate($file_name)
  {
    if(WactTemplate :: isFileReadable($this->templates_dir . '/' . $file_name))
      return $this->templates_dir . '/' . $file_name;
  }

  public function readTemplateFile($file_name)
  {
    if(WactTemplate :: isFileReadable($file_name))
      return file_get_contents($file_name, 1);
  }
}

