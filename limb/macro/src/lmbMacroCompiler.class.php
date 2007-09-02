<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/macro/src/lmbMacroTreeBuilder.class.php');
lmb_require('limb/macro/src/lmbMacroNode.class.php');
lmb_require('limb/macro/src/lmbMacroParser.class.php');
lmb_require('limb/macro/src/lmbMacroCodeWriter.class.php');

/**
 * class lmbMacroCompiler.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroCompiler
{
  /**
  * @var lmbMacroTreeBuilder
  */
  protected $tree_builder;

  /**
  * @var lmbMacroTemplateLocator
  */
  protected $template_locator;

  /**
  * @var lmbMacroSourceParser
  */
  protected $parser;

  /**
  * @var lmbMacroTagDictionary
  */
  protected $tag_dictionary;


  function __construct($tag_dictionary, $template_locator)
  {
    $this->template_locator = $template_locator;
    $this->tag_dictionary = lmbMacroTagDictionary :: instance();
    $this->tree_builder = new lmbMacroTreeBuilder($this);
  }

  function compile($file_name)
  {
    if(!$source_file_path = $this->template_locator->locateSourceTemplate($file_name))    
     throw new lmbMacroException('Template source file not found', array('file_name' => $file_name));

    $root_node = new lmbMacroNode(new lmbMacroSourceLocation($source_file_path, ''));
    $this->parseTemplate($file_name, $root_node);
    $root_node->prepare();

    $compiled_file_path = $this->template_locator->locateCompiledTemplate($file_name);
    list($class, $render_func, $generated_code) = $this->_generateTemplateCode(md5($compiled_file_path), $root_node);
    self :: writeFile($compiled_file_path, $generated_code);
    return array($class, $render_func, $compiled_file_path);
  }

  function _generateTemplateCode($prefix, $root_node)
  {
    $code_writer = new lmbMacroCodeWriter();
    $code_writer->registerInclude('limb/macro/src/lmbMacroTemplateExecutor.class.php');
    $class = $code_writer->beginClass('TemplateExecutor' . $prefix, 'lmbMacroTemplateExecutor');
    $render_func = $code_writer->beginFunction('render');
    $root_node->generate($code_writer);
    $code_writer->endFunction();
    $code_writer->endClass();
    return array($class, $render_func, $code_writer->renderCode());
  }

  function parseTemplate($source_file_path, $root_node)
  {
    $parser = new lmbMacroParser($this->tree_builder, $this->template_locator, $this->tag_dictionary);
    $parser->parse($source_file_path, $root_node);
  }

  /**
  * @return lmbMacroTemplateLocator
  */
  function getTemplateLocator()
  {
    return $this->template_locator;
  }

  /**
  * @return lmbMacroTreeBuilder
  */
  function getTreeBuilder()
  {
    return $this->tree_builder;
  }

  function getTagDictionary()
  {
    return $this->tag_dictionary;
  }

  static function writeFile($file, $data)
  {
    $dirname = dirname($file);    
    lmbFs :: mkdir($dirname);

    file_put_contents($file, $data);
  }
}

