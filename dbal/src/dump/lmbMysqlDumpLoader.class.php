<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/dump/lmbSQLDumpLoader.class.php');

/**
 * class lmbMysqlDumpLoader.
 *
 * @package dbal
 * @version $Id: lmbMysqlDumpLoader.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbMysqlDumpLoader extends lmbSQLDumpLoader
{
  // the parsing code was taken from somewhere else...
  // and it's ugly, ugly, ugly!!!
  // use some sort of lexer later...
  protected function _retrieveStatements($sql)
  {
    $ret = array();

    $sql          = trim($sql);
    $sql_len      = strlen($sql);
    $char         = '';
    $string_start = '';
    $in_string    = false;

    for($i = 0; $i < $sql_len; ++$i)
    {
      $char = $sql[$i];

      // We are in a string, check for not escaped end of strings except for
      // backquotes that can't be escaped
      if($in_string)
      {
        for(;;)
        {
          $i = strpos($sql, $string_start, $i);
          // No end of string found->add the current substring to the
          // returned array
          if (!$i)
          {
            $ret[] = $sql;
            return $ret;
          }
          // Backquotes or no backslashes before quotes: it's indeed the
          // end of the string->exit the loop
          elseif ($string_start == '`' || $sql[$i-1] != '\\')
          {
            $string_start      = '';
            $in_string         = false;
            break;
          }
          // one or more Backslashes before the presumed end of string...
          else
          {
            // ... first checks for escaped backslashes
            $j                     = 2;
            $escaped_backslash     = false;
            while ($i-$j > 0 && $sql[$i-$j] == '\\')
            {
              $escaped_backslash = !$escaped_backslash;
              $j++;
            }
            // ... if escaped backslashes: it's really the end of the
            // string->exit the loop
            if ($escaped_backslash)
            {
              $string_start  = '';
              $in_string     = false;
              break;
            }
            else
              $i++;
          }
        }
      }
      // We are not in a string, first check for delimiter...
      elseif($char == ';')
      {
        // if delimiter found, add the parsed part to the returned array
        $ret[]      = substr($sql, 0, $i);
        $sql        = ltrim(substr($sql, min($i + 1, $sql_len)));
        $sql_len    = strlen($sql);
        if($sql_len)
          $i = -1;
        else
          return $ret;
      }
      // ... then check for start of a string,...
      elseif (($char == '"') || ($char == '\'') || ($char == '`'))
      {
        $in_string    = true;
        $string_start = $char;
      }
      // ... for start of a comment (and remove this comment if found)...
      elseif ($char == '#'
               || ($char == ' ' && $i > 1 && $sql[$i-2] . $sql[$i-1] == '--'))
      {
        // starting position of the comment depends on the comment type
        $start_of_comment = (($sql[$i] == '#') ? $i : $i-2);
        $end_of_comment = self :: _getEndOfCommentPosition($sql, $i+2);

        if(!$end_of_comment)
        {
          // no eol found after '#', add the parsed part to the returned
          // array if required and exit
          if($start_of_comment > 0)
            $ret[] = trim(substr($sql, 0, $start_of_comment));

          return $ret;
        }
        else
        {
          $sql = substr($sql, 0, $start_of_comment) . ltrim(substr($sql, $end_of_comment));
          $sql_len = strlen($sql);
          $i--;
        }
      }
      // ... for start of a comment /* and remove it
      elseif($char == '/' && isset($sql[$i+1]) && $sql[$i+1] == '*')
      {
        $start_of_comment = $i;
        $end_of_comment = self :: _getEndOfCommentPosition($sql, $i+2);

        if(!$end_of_comment)
        {
          // no eol found after '#', add the parsed part to the returned
          // array if required and exit
          if($start_of_comment > 0)
            $ret[] = trim(substr($sql, 0, $start_of_comment));

          return $ret;
        }
        else
        {
          $sql = substr($sql, 0, $start_of_comment) . ltrim(substr($sql, $end_of_comment));
          $sql_len = strlen($sql);
          $i--;
        }
      }
    }
    // add any rest to the returned array
    if (!empty($sql) && preg_match('/\S+/', $sql))
      $ret[] = $sql;

    return $ret;
  }

  protected function _getEndOfCommentPosition($str, $start)
  {
    // if no "\n" exits in the remaining string, checks for "\r"
    // (Mac eol style)

    if($pos = strpos('  ' . $str, "*/\012", $start))
      return $pos;
    if($pos = strpos('  ' . $str, "*/\015", $start))
      return $pos;
    if($pos = strpos('  ' . $str, "*/;", $start))
      return $pos+1;

    return false;
  }

  protected function _processTableName($table)
  {
    return trim($table, '`');
  }
}

