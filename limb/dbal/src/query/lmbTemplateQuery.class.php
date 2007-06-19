<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbTemplateQuery.
 *
 * @package dbal
 * @version $Id: lmbTemplateQuery.class.php 6005 2007-06-19 21:14:49Z pachanga $
 */
class lmbTemplateQuery
{
  protected $_template_sql;
  protected $_no_hints_sql;
  protected $_conn;
  protected $_hints;

  function __construct($template_sql, $conn)
  {
    $this->_template_sql = $template_sql;
    $this->_conn = $conn;
  }

  protected function _declareHints()
  {
    if($this->_hints !== null)
      return $this->_hints;

    if(preg_match_all('~%([a-z_]+)%~', $this->_template_sql, $m))
      $this->_hints = $m[1];
    else
      $this->_hints = array();
    return $this->_hints;
  }

  function _wrapHint($hint)
  {
    return "%$hint%";
  }

  function _getWrappedHints()
  {
    return array_map(array($this, '_wrapHint'), $this->_declareHints());
  }

  function _fillHints()
  {
    $hints = $this->_declareHints();
    $result = array();
    foreach($hints as $hint)
    {
      $method = '_get' . lmb_camel_case($hint) . 'Hint';
      $result[$this->_wrapHint($hint)] = $this->$method();
    }
    return $result;
  }

  function toString()
  {
    $hints = $this->_fillHints();
    return trim(strtr($this->_template_sql, $hints));
  }

  function getStatement()
  {
    return $this->_conn->newStatement($this->toString());
  }

  protected function _getNoHintsSQL()
  {
    if($this->_no_hints_sql)
      return $this->_no_hints_sql;

    $result = array();
    foreach($this->_getWrappedHints() as $hint)
      $result[$hint] = '';

    $this->_no_hints_sql = strtr($this->_template_sql, $result);
    return $this->_no_hints_sql;
  }
}
?>
