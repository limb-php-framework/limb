<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/fs/src/lmbFs.class.php');

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
  * @var lmbMacroTemplateLocatorInterface
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

  /**
  * @var lmbMacroFilterDictionary
  */
  protected $filter_dictionary;

  function __construct($tag_dictionary, $template_locator, $filter_dictionary)
  {
    $this->tree_builder = new lmbMacroTreeBuilder($this);

    $this->template_locator = $template_locator;

    $this->tag_dictionary = $tag_dictionary;
    $this->filter_dictionary = $filter_dictionary;
  }

  function compile($source_file, $compiled_file, $class, $render_func)
  {
    $root_node = new lmbMacroNode(new lmbMacroSourceLocation($source_file, ''));
    $this->parseTemplate($source_file, $root_node);

    $generated_code = $this->_generateTemplateCode($class, $render_func, $root_node);
    self :: writeFile($compiled_file, $generated_code);
  }

  function _generateTemplateCode($class, $render_func, $root_node)
  {
    $code_writer = new lmbMacroCodeWriter($class, $render_func);
    $root_node->generate($code_writer);
    return $code_writer->renderCode();
  }

  function parseTemplate($file_name, $root_node)
  {
    if(!$source_file_path = $this->template_locator->locateSourceTemplate($file_name))
          throw new lmbMacroException('Template source file not found', array('file_name' => $file_name));
    $parser = new lmbMacroParser($this->tree_builder, $this->tag_dictionary);
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

  function getFilterDictionary()
  {
    return $this->filter_dictionary;
  }

  static function writeFile($file, $data)
  {
    
    $dirname = dirname($file);
    lmbFs :: mkdir($dirname);

    file_put_contents($file, $data);
  }
}

