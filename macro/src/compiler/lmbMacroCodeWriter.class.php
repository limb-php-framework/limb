<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbMacroCodeWriter.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroCodeWriter
{
  const MODE_PHP = 1;
  const MODE_HTML = 2;

  protected $class;

  protected $parent;

  protected $current_mode = self :: MODE_PHP;

  protected $current_method;

  protected $code = '';

  protected $methods = array();

  protected $init_code = '';

  protected $methods_stack = array();

  protected $include_list = array();

  protected $temp_var_name = 1;

  function __construct($class, $render_func = 'render')
  {
    $this->class = $class;
    $this->render_func = $render_func;
    $this->parent = 'lmbMacroTemplateExecutor';
    $this->registerInclude('limb/macro/src/compiler/lmbMacroTemplateExecutor.class.php');

    $this->beginMethod($render_func, array('$args = array()'));
    $this->writePHP('if($args) extract($args);'."\n");
    $this->writePHP('$this->_init();'."\n");
  }

  function getClass()
  {
    return $this->class;
  }

  function getRenderMethod()
  {
    return 'render';
  }

  protected function switchToPHP()
  {
    if($this->current_mode == self :: MODE_HTML)
    {
      $this->current_mode = self :: MODE_PHP;
      $this->_append('<?php ');
    }
  }

  protected function switchToHTML($context = null)
  {
    if($this->current_mode == self :: MODE_PHP)
    {
      $this->current_mode = self :: MODE_HTML;
      if($context === "\n")
        $this->_append(" ?>\n");
      else
        $this->_append(' ?>');
    }
  }

  function writePHP($text)
  {
    $this->switchToPHP();
    $this->_append($text);
  }

  function writePHPLiteral($text, $escape_text = true)
  {
    $this->switchToPHP();

    if($escape_text)
      $this->_append("'" . $this->escapeLiteral($text) . "'");
    else
      $this->_append("'" . $text . "'");
  }

  function escapeLiteral($text)
  {
    $text = str_replace('\'', "\\'", $text);
    if(substr($text, -1) == '\\')
      $text .= '\\';
    return $text;
  }

  function writeHTML($text)
  {
    $this->switchToHTML(substr($text,0,1));
    $this->_append($text);
  }
  
  function writeRaw($text)
  {
    $this->_append($text);
  }

  function renderCode()
  {
    $this->endMethod();

    $code = "<?php\n" .
           //protection from self inclusion
           "if(!class_exists('{$this->class}', false)){\n" .
           $this->_renderIncludeList() . 
           "class {$this->class} " . ($this->parent ? "extends {$this->parent} " : '') . "{\n" .
           (!$this->init_code ? "" :
           "\nfunction _init() {" .
           "\n$this->init_code\n" .
           "}\n" 
           ) .
           $this->_renderMethods() . 
           "\n}" . 
           "\n}";
    return $code;
  }

  function getCode()
  {
    return $this->code;
  }

  function getMode()
  {
    return $this->current_mode;
  }

  function registerInclude($include_file)
  {
    if(!in_array($include_file, $this->include_list))
      $this->include_list[] = $include_file;    
  }

  function getIncludeList()
  {
    return $this->include_list;
  }

  function beginFunction($name, $param_list = array())
  {
    $this->writePHP('function ' . $name . '(' . implode(',', $param_list) .") {\n");
    return $name;
  }

  function endFunction()
  {
    $this->writePHP("\n}\n");
  }

  function beginMethod($name, $param_list = array())
  {
    $this->methods_stack[] = array($this->current_method, $this->current_mode);
    $this->current_method = $name;

    //we don't need to switch to PHP, since methods can be declared inside PHP only
    $this->writeRaw('function ' . $name . '(' . implode(',', $param_list) .") {\n");
    $this->current_mode = self :: MODE_PHP;
    return $name;
  }

  function endMethod()
  {
    $this->writePHP("\n}\n");
    list($this->current_method, $this->current_mode) = array_pop($this->methods_stack);
  }

  function writeToInit($code)
  {
    $this->init_code .= $code;
  }

  /**
  * Utility method, which generates a unique variable name
  */
  function generateTempName()
  {
    $var = $this->temp_var_name++;
    if($var > 675)
      return chr(65 + ($var/26)/26) . chr(65 + ($var/26)%26) . chr(65 + $var%26);
    elseif($var > 26)
      return chr(65 + ($var/26)%26) . chr(65 + $var%26);
    else
      return chr(64 + $var);
  }

  /**
  * Utility method, which generates a unique variable name, prefixed with a $
  */
  function generateVar()
  {
    return '$' . $this->generateTempName();
  }

  protected function _append($code)
  {
    if(!$this->current_method)
    {
      $this->code .= $code;
      return;
    }

    if(!isset($this->methods[$this->current_method]))
      $this->methods[$this->current_method] = '';

    $this->methods[$this->current_method] .= $code;
  }

  protected function _renderMethods()
  {
    return implode("\n", $this->methods);
  }

  protected function _renderIncludeList()
  {
    $include_code = '';
    foreach($this->include_list as $include_file)
      $include_code .= "require_once('$include_file');\n";
    return $include_code;
  }
}

