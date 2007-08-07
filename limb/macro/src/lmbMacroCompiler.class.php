<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

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
  * @var lmbMacroConfig
  */
  protected $config;

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


  function __construct($config, $tag_dictionary, $template_locator)
  {
    $this->config = $config;
    $this->template_locator = $template_locator;

    $this->tag_dictionary = $tag_dictionary;
    $this->tree_builder = new lmbMacroTreeBuilder($this);
  }

  function compile($file_name)
  {
    if(!$source_file_path = $this->template_locator->locateSourceTemplate($file_name))    
     throw new lmbMacroException('Template source file not found', array('file_name' => $file_name));

    $root_node = new lmbMacroRootNode(new lmbMacroSourceLocation($source_file_path, ''));

    $this->parseTemplate($file_name, $root_node);

    $root_node->prepare();

    $compiled_file_path = $this->template_locator->locateCompiledTemplate($file_name);
    $generated_code = $this->_generateTemplateCode(md5($compiled_file_path), $root_node);
    self :: writeFile($compiled_file_path, $generated_code);
  }

  function _generateTemplateCode($prefix, $root_node)
  {
    $code_writer = new lmbMacroCodeWriter();
    $code_writer->setFunctionPrefix($prefix);

    $constructor_func = $code_writer->beginFunction('($root, $components)');
    $root_node->generateConstructor($code_writer);
    $code_writer->endFunction();

    $render_func = $code_writer->beginFunction('($root, $components)');
    $code_writer->writePHP('$template = $root;' . "\n");
    $root_node->generate($code_writer);
    $code_writer->endFunction();

    $code_writer->writePHP('$GLOBALS[\'TemplateRender\'][$compiled_template_path] = \'' . $render_func . '\';');
    $code_writer->writePHP('$GLOBALS[\'TemplateConstruct\'][$compiled_template_path] = \'' . $constructor_func . '\';');

    return $code_writer->renderCode();
  }

  function parseTemplate($source_file_path, $root_node)
  {
    $parser = new lmbMacroParser($this->tree_builder,                                           
                                 $this->config,
                                 $this->template_locator,
                                 $this->tag_dictionary);

    $parser->parse($source_file_path, $root_node);
  }

  /**
  * @return lmbMacroConfig
  */
  function getConfig()
  {
    return $this->config;
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

