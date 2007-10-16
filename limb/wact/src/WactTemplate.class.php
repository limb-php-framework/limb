<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once('limb/wact/src/components/components.inc.php');

/**
 * class WactTemplate.
 *
 * @package wact
 * @version $Id: WactTemplate.class.php 6426 2007-10-16 10:36:28Z serega $
 */
class WactTemplate extends WactDatasourceRuntimeComponent
{
  protected $template_path;

  protected $render_function;

  protected $config;

  protected $locator;

  protected $components;

  function __construct($template_path, $config = null, $locator = null)
  {
    parent :: __construct('root');

    if(!is_object($config))
    {
      require_once('limb/wact/src/WactDefaultTemplateConfig.class.php');
      $config = new WactDefaultTemplateConfig();
    }

    if(!is_object($locator))
    {
      require_once('limb/wact/src/locator/WactDefaultTemplateLocator.class.php');
      $locator = new WactDefaultTemplateLocator($config);
    }

    $this->config = $config;
    $this->locator = $locator;

    $this->template_path = $template_path;

    $compiled_template_path = $this->locator->locateCompiledTemplate($this->template_path);

    if(!isset($GLOBALS['TemplateRender'][$compiled_template_path]))
    {
      if(($this->config->isForceCompile()) || !file_exists($compiled_template_path))
      {
        $compiler = $this->createCompiler();
        $compiler->compile($this->template_path);
      }

      include_once($compiled_template_path);
    }

    $this->render_function = $GLOBALS['TemplateRender'][$compiled_template_path];
    $func = $GLOBALS['TemplateConstruct'][$compiled_template_path];
    $this->components = array();
    $func($this, $this->components);
  }

  function createCompiler()
  {
    require_once 'limb/wact/src/compiler/templatecompiler.inc.php';
    return new WactCompiler($this->config, $this->locator);
  }

  static function getValue($source, $value)
  {
    if(is_scalar($source) || is_null($source))
      return null;

    if((is_array($source) || $source instanceof ArrayAccess))
    {
      if(isset($source[$value]))
        return $source[$value];
      else
        return null;
    }

    if (is_object($source) && method_exists($source, 'get'))
      return $source->get($value);

    return null;
  }

  static function setValue(&$source, $field, $value)
  {
    if(is_scalar($source) || is_null($source))
      return;

    if((is_array($source) || $source instanceof ArrayAccess))
    {
      $source[$field] = $value;
      return;
    }

    if (is_object($source) && method_exists($source, 'set'))
    {
      $source->set($field, $value);
      return;
    }
  }

  /**
  * @return WactArrayIterator/Iterator
  **/
  static function castToIterator($value)
  {
    if(!$value || is_scalar($value))
      return new WactArrayIterator(array());

    if(is_array($value))
      return new WactArrayIterator($value);

    if($value instanceof IteratorAggregate)
      return $value->getIterator();

    return $value;
  }

  function display()
  {
    $func = $this->render_function;
    $func($this, $this->components);
  }

  function capture()
  {
    ob_start();
    try
    {
      $this->display();
    }
    catch(WactException $e)
    {
      ob_end_flush();
      throw $e;
    }
    return ob_get_clean();
  }

  function getTemplatePath()
  {
    return $this->locator->locateSourceTemplate($this->template_path);
  }

  static function toStudlyCaps($str)
  {
    return preg_replace('~([a-zA-Z])?_([a-zA-Z])~e', "'\\1'.strtoupper('\\2')", $str);
  }

  static function isFileReadable($file)
  {
    $fh = @fopen($file, 'r', true);
    if(!is_resource($fh))
      return false;

    fclose($fh);
    return true;
  }

  static function escape($string)
  {
    $string = htmlspecialchars($string, ENT_QUOTES);
    if(strpos($string, '&amp;#') !== FALSE)
      $string = preg_replace('/&amp;#([^;]*);/', '&#$1;', $string);
    return $string;
  }
}



