<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbCollectionDecorator.class.php');

/**
 * class lmbSearchResultProcessor.
 *
 * @package search
 * @version $Id: lmbSearchResultProcessor.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class lmbSearchResultProcessor extends lmbCollectionDecorator
{
  protected $radius = 0;
  protected $gaps_pattern = '';
  protected $lines_limit = 0;
  protected $match_left_mark = '';
  protected $match_right_mark = '';
  protected $words = array();
  protected $regex;
  protected $content_field = 'content';

  function setMatchedWordFoldingRadius($radius)
  {
    $this->radius = $radius;
  }

  function setMatchLeftMark($value)
  {
    $this->match_left_mark = $value;
  }

  function setMatchRightMark($value)
  {
    $this->match_right_mark = $value;
  }

  function setGapsPattern($gaps_pattern)
  {
    $this->gaps_pattern = $gaps_pattern;
  }

  function setMatchingLinesLimit($limit)
  {
    $this->lines_limit = $limit;
  }

  function setContentField($value)
  {
    $this->content_field = $value;
  }

  function setWords($words)
  {
    $this->words = $words;
    usort($words, array($this, "_usortHandler"));
  }

  function rewind()
  {
    $this->_formRegex();
    return parent :: rewind();
  }

  protected function _formRegex()
  {
    $regex = '';
    foreach($this->words as $word)
      $regex .= preg_quote($word) . '|';

    $this->regex = '~(.*?)(' . rtrim($regex, '|') . ')(.*)~si';
  }

  function current()
  {
    $record = parent :: current();

    $this->_process($record);

    return $record;
  }

  protected function _process($record)
  {
    if(!$text = $record->get($this->content_field))
      return;

    $result = '';
    $lines_count = 0;

    while(preg_match($this->regex, $text, $matches))
    {
      if(($lines_count >= $this->lines_limit) && $this->lines_limit)
        break;

      $chunk = $matches[1];

      if($lines_count == 0)
        $result .= $this->_makeGap('left', $chunk);
      else
        $result .= $this->_makeGap('middle', $chunk);

      $result .= $this->match_left_mark . $matches[2] . $this->match_right_mark;

      $text = $matches[3];
      $lines_count++;
    }

    $result .= $this->_makeGap('right', $text);

    $record->set($this->content_field, $result);
  }

  function _makeGap($gap_pos, $text_chunk)
  {
    $result = '';
    $chunk_len = lmb_strlen($text_chunk);

    if($gap_pos == 'middle')
    {
      if($chunk_len > 2*$this->radius)
        $result = lmb_substr($text_chunk, 0, $this->radius) .
                  $this->gaps_pattern .
                  lmb_substr($text_chunk, (-1)*$this->radius, $this->radius);
      else
        $result = $text_chunk;
    }
    elseif($gap_pos == 'left')
    {
      if($chunk_len > $this->radius)
        $result = $this->gaps_pattern .
                  lmb_substr($text_chunk, (-1)*$this->radius, $this->radius);
     else
       $result = $text_chunk;
    }
    elseif($gap_pos == 'right')
    {
      if($chunk_len > $this->radius)
        $result = lmb_substr($text_chunk, 0, $this->radius) .
                  $this->gaps_pattern;
     else
       $result = $text_chunk;
    }

    return $result;
  }

  function _usortHandler($a, $b)
  {
   if (lmb_strlen($a) == lmb_strlen($b))
     return 0;

   return (lmb_strlen($a) > lmb_strlen($b)) ? -1 : 1;
  }
}


