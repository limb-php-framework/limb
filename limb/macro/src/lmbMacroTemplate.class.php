<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/lmbMacroTemplateLocator.class.php');
lmb_require('limb/macro/src/lmbMacroCompiler.class.php');
lmb_require('limb/macro/src/lmbMacroTagDictionary.class.php');
lmb_require('limb/macro/src/lmbMacroFilterDictionary.class.php');
lmb_require('limb/macro/src/lmbMacroConfig.class.php');
lmb_require('limb/macro/src/lmbMacroException.class.php');

/**
 * class lmbMacroTemplate.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroTemplate
{
  protected $file;
  protected $compiled_file;
  protected $executor;
  protected $vars = array();
  protected $child_executor;

  function __construct($file, lmbMacroConfig $config = null)
  {
    $this->file = $file;
    $this->config = $config ? $config : new lmbMacroConfig();
    $this->locator = new lmbMacroTemplateLocator($this->config);
  }

  static function locateTemplateByAlias($alias, lmbMacroConfig $config = null)
  {
    $config = $config ? $config : new lmbMacroConfig();
    $locator = new lmbMacroTemplateLocator($config);
    return $locator->locateSourceTemplate($alias);
  }

  function setVars($vars)
  {
    $this->vars = $vars;
  }

  function set($name, $value)
  {
    $this->vars[$name] = $value;
  }

  function setChildExecutor($executor)
  {
    $this->child_executor = $executor;
  }

  function render($vars = array())
  {     
    if(!$this->executor)
    {
      $this->compiled_file = $this->locator->locateCompiledTemplate($this->file);

      if($this->config->isForceCompile() || !file_exists($this->compiled_file))
      {
        if(!$source_file = $this->locator->locateSourceTemplate($this->file))
          throw new lmbMacroException('Template source file not found', array('file_name' => $this->file));

        $macro_executor_class = 'MacroTemplateExecutor' . uniqid();//think about evaling this instance

        $compiler = $this->_createCompiler();
        $compiler->compile($source_file, $this->compiled_file, $macro_executor_class, 'render');
        //appending macro executor class
        file_put_contents($this->compiled_file, file_get_contents($this->compiled_file) .
                                          "\n\$macro_executor_class='$macro_executor_class';");
      }

      include($this->compiled_file);
      $this->executor = new $macro_executor_class($this->config);
    }

    $this->executor->setVars($this->vars);

    //in case of dynamic wrapping we need to ask parent for all unknown variables
    if($this->child_executor)
      $this->child_executor->setContext($this->executor);

    ob_start();
    $this->executor->render($vars);
    $out = ob_get_contents();
    ob_end_clean();
    return $out;
  }

  protected function _createCompiler()
  {
    $tag_dictionary = lmbMacroTagDictionary :: load($this->config);
    $filter_dictionary = lmbMacroFilterDictionary :: load($this->config);
    return new lmbMacroCompiler($tag_dictionary, $this->locator, $filter_dictionary);
  }
}

