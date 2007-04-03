<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactCompiler.class.php 5513 2007-04-03 07:37:51Z pachanga $
 * @package    wact
 */

class WactCompiler
{
  /**
  * @var WactTreeBuilder
  */
  protected $tree_builder;

  /**
  * @var WactConfig
  */
  protected $config;

  /**
  * @var WactTemplateLocator
  */
  protected $template_locator;

  /**
  * @var WactSourceParser
  */
  protected $parser;

  /**
  * @var WactTagDictionary
  */
  protected $tag_dictionary;

  /**
  * @var WactPropertyDictionary
  */
  protected $property_dictionary;

    /**
  * @var WactFilterDictionary
  */
  protected $filter_dictionary;

  function __construct($config, $template_locator)
  {
    $this->config = $config;
    $this->template_locator = $template_locator;

    WactDictionaryHolder :: initialize($config);
    $dictionary_holder = WactDictionaryHolder :: instance();

    $this->tag_dictionary = $dictionary_holder->getTagDictionary();
    $this->property_dictionary = $dictionary_holder->getPropertyDictionary();
    $this->filter_dictionary = $dictionary_holder->getFilterDictionary();

    $this->tree_builder = new WactTreeBuilder($this);
  }

  function compile($file_name)
  {
    $source_file_path = $this->template_locator->locateSourceTemplate($file_name);

    if(empty($source_file_path))
        throw new WactException('Template source file not found', array('file_name' => $file_name));

    $root_node = new WactCompileTreeRootNode(new WactSourceLocation($source_file_path, ''));

    $this->parseTemplate($file_name, $root_node);

    $root_node->prepare();

    $compiled_file_path = $this->template_locator->locateCompiledTemplate($file_name);

    $generated_code = $this->_generateTemplateCode(md5($compiled_file_path), $root_node);

    self :: writeFile($compiled_file_path, $generated_code);
  }

  function _generateTemplateCode($prefix, $root_node)
  {
    $code_writer = new WactCodeWriter();
    $code_writer->setFunctionPrefix($prefix);

    $constructor_func = $code_writer->beginFunction('($root, &$components)');
    $root_node->generateConstructor($code_writer);
    $code_writer->endFunction();

    $render_func = $code_writer->beginFunction('($root, &$components)');
    $code_writer->writePHP('$template = $root;' . "\n");
    $root_node->generate($code_writer);
    $code_writer->endFunction();

    $code_writer->writePHP('$GLOBALS[\'TemplateRender\'][$compiled_template_path] = \'' . $render_func . '\';');
    $code_writer->writePHP('$GLOBALS[\'TemplateConstruct\'][$compiled_template_path] = \'' . $constructor_func . '\';');

    return $code_writer->renderCode();
  }

  function parseTemplate($source_file_path, $root_node)
  {
    $parser = new WactSourceFileParser($this->tree_builder,
                                       $this->_createNodeBuilder(),
                                       $this->config,
                                       $this->template_locator,
                                       $this->tag_dictionary);

    $parser->parse($source_file_path, $root_node);
  }

  /**
  * @return WactNodeBuilder
  */
  protected function _createNodeBuilder()
  {
    $node_builder = new WactNodeBuilder($this->tree_builder,
                                        $this->property_dictionary,
                                        $this->filter_dictionary);
    return $node_builder;
  }

  /**
  * @return WactConfig
  **/
  function getConfig()
  {
    return $this->config;
  }

  /**
  * @return WactTemplateLocator
  **/
  function getTemplateLocator()
  {
    return $this->template_locator;
  }

  /**
  * @return WactTreeBuilder
  **/
  function getTreeBuilder()
  {
    return $this->tree_builder;
  }

  function getFilterDictionary()
  {
    return $this->filter_dictionary;
  }

  function getTagDictionary()
  {
    return $this->tag_dictionary;
  }

  function getPropertyDictionary()
  {
    return $this->property_dictionary;
  }

  static function writeFile($file, $data)
  {
    $dirname = dirname($file);
    if(!is_dir($dirname))
      self :: _makeDir($dirname);

    file_put_contents($file, $data);
  }

  protected static function _makeDir($dirname)
  {
    $path_elements = explode('/', $dirname);
    $index = self :: _getFirstExistingPathIndex($path_elements);

    if($index === false)
      throw new lmbIOException('cant find first existent path', array('dir' => $dir));

    $offset_path = '';
    for($i=0; $i < $index; $i++)
      $offset_path .= $path_elements[$i] . '/';

    for($i=$index; $i < count($path_elements); $i++)
    {
      $offset_path .= $path_elements[$i] . '/';
      self :: _doMkdir($offset_path, $perm = 0777);
    }
  }

  protected static function _doMkdir($dir, $perm)
  {
    if(is_dir($dir))
      return;

    $oldumask = umask(0);
    if(!mkdir($dir, $perm))
    {
      umask($oldumask);
      throw new lmbIOException('failed to create directory', array('dir' => $dir));
    }

    umask($oldumask);
  }

  protected static function _getFirstExistingPathIndex($path_elements)
  {
    for($i = count($path_elements); $i > 0; $i--)
    {
      $path = implode('/', $path_elements);

      if(is_dir($path))
        return $i;

      array_pop($path_elements);
    }

    if($path{0} == '/')
        return false;
    else
      return 0;
  }
}
?>
