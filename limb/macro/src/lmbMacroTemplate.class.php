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

/**
 * class lmbMacroTemplate.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroTemplate
{
  protected $file;
  protected $cache_dir;
  protected $vars = array();
  protected $child_executor;
  protected $tag_dictionary;

  function __construct($file, $cache_dir = null, $locator = null, $tag_dictionary = null)
  {
    $this->file = $file;

    if(!$cache_dir)
      $cache_dir = LIMB_VAR_DIR . '/compiled';
    $this->cache_dir = $cache_dir;

    if(!$locator)
      $locator = new lmbMacroTemplateLocator();
    $this->locator = $locator;

    if(!$tag_dictionary)
      $tag_dictionary = lmbMacroTagDictionary :: instance();
    $this->tag_dictionary = $tag_dictionary;
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
    if(!$source_file = $this->locator->locateSourceTemplate($this->file))    
     throw new lmbMacroException('Template source file not found', array('file_name' => $this->file));

    $compiled_file = $this->locator->locateCompiledTemplate($this->file);

    $class = 'MacroTemplateExecutor' . uniqid();//think about evaling this instance

    $compiler = $this->_createCompiler();
    $compiler->compile($source_file, $compiled_file, $class, 'render');

    include($compiled_file);
    $executor = new $class($this->vars, $this->cache_dir, $this->locator);

    //in case of dynamic wrapping we need to ask parent for all unknown variables
    if($this->child_executor)
      $this->child_executor->setContext($executor);

    ob_start();
    $executor->render($vars);
    $out = ob_get_contents();
    ob_end_clean();
    return $out;
  }

  protected function _createCompiler()
  {
    return new lmbMacroCompiler($this->tag_dictionary, $this->locator);
  }
}

