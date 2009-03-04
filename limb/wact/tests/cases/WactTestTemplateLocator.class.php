<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/locator/WactTemplateLocator.interface.php');

class WactTestTemplateLocator implements WactTemplateLocator
{
  /**
   * @var WactTemplateConfig
   */
  protected $config;

  protected $templates = array();
  protected $file_names = array();

  public function __construct($config)
  {
    $this->config = $config;
  }

  public function locateCompiledTemplate($file_name)
  {
    $file_path = $this->file_names[$file_name];
    return $this->config->getCacheDir(). '/' . $file_path . '.php';
  }

  public function registerTestingTemplate($file_path, $template, $file_name = '')
  {
    if(!$file_name)
      $file_name = $file_path;

    $this->file_names[$file_name] = $file_path;
    $this->templates[$file_path] = $template;
  }

  public function locateSourceTemplate($file_name)
  {
    if (isset($this->file_names[$file_name]))
      return $this->file_names[$file_name];
  }

  public function readTemplateFile($file_path)
  {
    if (isset($this->templates[$file_path]))
      return $this->templates[$file_path];
  }

  public function clearTestingTemplates()
  {
    foreach(array_keys($this->templates) as $file_name)
    {
      $compiled = $this->config->getCacheDir().'/'.$file_name.'.php';
      if(file_exists($compiled))
        unlink($compiled);
    }
    clearstatcache();

    $this->templates = array();
  }
}

