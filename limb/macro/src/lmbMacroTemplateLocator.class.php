<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactDefaultTemplateLocator.class.php 5420 2007-03-29 12:45:34Z serega $
 * @package    macro
 */

class lmbMacroTemplateLocator
{
  protected $config;
  protected $templates_dir;

  public function __construct($config)
  {
    $this->config = $config;
    $this->templates_dir = 'templates/';//fix it
  }

  public function locateCompiledTemplate($file_name)
  {
    return $this->config->getCacheDir() . '/' . md5($file_name) . '.php';
  }

  public function locateSourceTemplate($file_name)
  {    
    return $this->templates_dir . '/' . $file_name;
  }

  public function readTemplateFile($file_name)
  {    
    return file_get_contents($file_name, 1);
  }
}
?>