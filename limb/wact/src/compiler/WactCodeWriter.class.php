<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class WactCodeWriter.
 *
 * @package wact
 * @version $Id: WactCodeWriter.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactCodeWriter
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
    if ($this->current_mode == self :: MODE_HTML)
    {
      $this->current_mode = self :: MODE_PHP;
      $this->code .= '<?php ';
    }
  }

  protected function switchToHTML($context = NULL)
  {
    if ($this->current_mode == self :: MODE_PHP)
    {
      $this->current_mode = self :: MODE_HTML;
      if ($context === "\n")
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

    if(is_numeric($text))
      $this->code .= $text;
    elseif ($escape_text)
      $this->code .= "'" . $this->escapeLiteral($text) . "'";
    else
      $this->code .= "'" . $text . "'";
  }

  function escapeLiteral($text)
  {
    $text = str_replace('\'', "\\'", $text);
    if ( substr($text, -1) == '\\')
        $text .= '\\';

    return $text;
  }

  function writeHTML($text)
  {
    $this->switchToHTML(substr($text,0,1));
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
      $include_code .= "require_once '$include_file';\n";

    if (!empty($include_code))
      $this->code = '<?php ' . $include_code . '?>' . $this->code;
  }

  function getMode()
  {
    return $this->current_mode;
  }

  function registerInclude($include_file)
  {
    $this->_replaceConstantsInString($include_file);

    if (!in_array($include_file, $this->include_list)) {
        $this->include_list[] = $include_file;
    }
  }

  function getIncludeList()
  {
    return $this->include_list;
  }

  function _replaceConstantsInString(&$string)
  {
    $constPos = strpos($string, '%');
    while (is_integer($constPos))
    {
      $constant = substr($string, $constPos+1, strpos($string, '%', $constPos+1) - $constPos - 1);
      if (defined($constant))
        $string = str_replace("%$constant%", constant($constant), $string);
      else
        throw new WactException('Constant is not defined', array('constant' => $constant));

      $constPos = strpos($string, '%');
    }
    return true;
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
    if ($var > 675)
      return chr(65 + ($var/26)/26) . chr(65 + ($var/26)%26) . chr(65 + $var%26);
    elseif ($var > 26)
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

