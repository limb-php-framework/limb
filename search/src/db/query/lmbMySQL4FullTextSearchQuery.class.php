<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/query/lmbSelectRawQuery.class.php');

/**
 * class lmbMySQL4FullTextSearchQuery.
 *
 * @package search
 * @version $Id: lmbMySQL4FullTextSearchQuery.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class lmbMySQL4FullTextSearchQuery extends lmbSelectRawQuery
{
  function __construct($table, $words, $use_boolean_mode = true, $conn)
  {
    $match = $this->_buildMatch($words, $use_boolean_mode);
    $where = $this->_buildWhere($words, $use_boolean_mode);

    $sql = "SELECT {$table}.*, {$match} %fields% FROM {$table} %tables% %left_join% ".
           "{$where} %where% %group% %having%  ORDER BY score DESC %order%";
    parent :: __construct($sql, $conn);
  }

  protected function _buildMatch($words, $use_boolean_mode)
  {
    $query_words = $this->_getQueryWords($words, $use_boolean_mode);

    if($use_boolean_mode)
      $boolean_mode = " IN BOOLEAN MODE";
    else
      $boolean_mode = '';

    return "(MATCH (content) AGAINST (\"{$query_words}\"{$boolean_mode})) as score";
  }

  protected function _buildWhere($words, $use_boolean_mode)
  {
    $query_words = $this->_getQueryWords($words, $use_boolean_mode);

    if($use_boolean_mode)
      $boolean_mode = " IN BOOLEAN MODE";
    else
      $boolean_mode = '';

    return "WHERE MATCH (content) AGAINST (\"{$query_words}\"{$boolean_mode})";
  }

  function _getQueryWords($words, $use_boolean_mode)
  {
    foreach($words as $key => $word)
      $words[$key] = mysql_escape_string($word);

    if($use_boolean_mode)
      return implode('* ', $words) . '*';
    else
      return implode(' ', $words);
  }
}

