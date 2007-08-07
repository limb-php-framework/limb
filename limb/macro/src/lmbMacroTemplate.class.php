<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

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
  protected $includes = array();

  function __construct($file, $cache_dir)
  {
    $this->file = $file;
    $this->cache_dir = $cache_dir;
  }

  function set($name, $value)
  {
    $this->vars[$name] = $value;
  }

  function render()
  {
    ob_start();
    $file = $this->_compile($class);
    include($file);
    $body = new $class($this->vars);
    $body->paint();
    $out = ob_get_contents();
    ob_end_clean();
    return $out;
  }

  protected function _compile(&$class_name)
  {
    $contents = file_get_contents($this->file);
    $prefix = 'p' . md5($this->file);
    $class_name = "{$prefix}Body";
    
    $this->_processVars($contents);
    $body = $this->_generateBody($class_name, $contents);

    $compiled_file = $this->cache_dir . '/' . $prefix . '.php';
    file_put_contents($compiled_file, $body, LOCK_EX);
    return $compiled_file;
  }

  protected function _generateBody($class_name, $contents)
  {
    $include_methods = '';
    foreach($this->includes as $name => $body)
      $include_methods .= "$body\n";

    $code = <<<EOD
<?php
class {$class_name}
{
  protected \$vars = array();

  function __construct(\$vars)
  {
    \$this->vars = \$vars;
  }

  function __get(\$name)
  {
    if(isset(\$this->vars[\$name]))
      return \$this->vars[\$name];
  }

  $include_methods

  function paint(){ ?>$contents<?php }
}
?>
EOD;
  return $code;
  }

  protected function _processVars(&$contents)
  {
    $contents = str_replace('<?=', '<?php echo ', $contents);
    $contents = preg_replace('~<\?(?!php|=)~', '<?php ', $contents);    
    $contents = str_replace('@$', '$this->', $contents);
    $contents = preg_replace_callback('~\{(\$[^\W]+)\}~', array($this, '_varSugarCallback'), $contents);
    $contents = preg_replace_callback('~\{([^\W]+\([^\}]+)\}~', array($this, '_functionSugarCallback'), $contents);
  }

  protected function _varSugarCallback($matches)
  {
    return '<?php echo ' . $matches[1] . ' ?>';
  }

  protected function _functionSugarCallback($matches)
  {
    return '<?php echo ' . $matches[1] . ' ?>';
  }

  protected function _includeCallback($matches)
  {
    if(!preg_match('~file=(?:"|\')([^"\']+)(?:"|\')~', $matches[0], $m))
      throw new lmbException('Invalid <%INCLUDE..>: ' . $matches[0]);

    $file = lmbFs :: normalizePath($m[1]);

    $args = '';
    if(preg_match('~args=(?:"|\')\(([^\)]+)\)(?:"|\')~', $matches[0], $m))
      $args = $m[1];

    $contents = file_get_contents($file);
    $this->_processIncludes($contents);
    $method_name = 'paintInclude' . sizeof($this->includes);
    $method_body = "function $method_name(){ \$args = func_get_args();extract(\$args);?>$contents<?php }";

    $this->includes[$method_name] = $method_body;

    return "<?php \$this->$method_name(array($args)); ?>";
  }
}

