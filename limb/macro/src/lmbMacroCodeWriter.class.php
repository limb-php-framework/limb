<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
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

  protected $current_mode = self :: MODE_HTML;

  protected $code = '';

  protected $function_prefix = '';
  protected $function_suffix = 1;

  protected $include_list = array();

  protected $tempVarName = 1;

  function reset()
  {
    $this->code = '';
    $this->current_mode = self :: MODE_HTML;
    $this->include_list = array();
  }

  protected function switchToPHP()
  {
    if($this->current_mode == self :: MODE_HTML)
    {
      $this->current_mode = self :: MODE_PHP;
      $this->code .= '<?php ';
    }
  }

  protected function switchToHTML($context = null)
  {
    if($this->current_mode == self :: MODE_PHP)
    {
      $this->current_mode = self :: MODE_HTML;
      if($context === "\n")
        $this->code .= " ?>\n";
      else
        $this->code .= ' ?>';
    }
  }

  function writePHP($text)
  {
    $this->switchToPHP();
    $this->code .= $text;
  }

  function writePHPLiteral($text, $escape_text = true)
  {
    $this->switchToPHP();

    if($escape_text)
      $this->code .= "'" . $this->escapeLiteral($text) . "'";
    else
      $this->code .= "'" . $text . "'";
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
    $this->code .= $text;
  }
  
  function writeRaw($text)
  {
    $this->code .= $text;
  }

  function renderCode()
  {
    $this->switchToHTML();

    $this->_prependIncludeListToCode();

    return $this->code;
  }

  function getCode()
  {
    return $this->code;
  }

  function setCode($code)
  {
    $this->code = $code;
  }

  protected function _prependIncludeListToCode()
  {
    $include_code = '';
    foreach($this->include_list as $include_file)
      $include_code .= "require_once('$include_file');\n";

    if(!empty($include_code))
      $this->code = '<?php ' . $include_code . '?>' . $this->code;
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

  /**
  * Begins writing a PHP function to the compiled template, using the
  * function_prefix and the function_suffix, the latter being post incremented
  * by one.
  */
  function beginFunction($param_list)
  {
      $func_name = 'tpl' . $this->function_prefix . $this->function_suffix++;
      $this->writePHP('function ' . $func_name . $param_list ." {\n");
      return $func_name;
  }

  function endFunction()
  {
    $this->writePHP(" }\n");
  }

  function setFunctionPrefix($prefix)
  {
    $this->function_prefix = $prefix;
  }

  /**
  * Utility method, which generates a unique variable name
  */
  function getTempVariable()
  {
    $var = $this->tempVarName++;
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
  function getTempVarRef()
  {
    return '$' . $this->getTempVariable();
  }
}
?>
