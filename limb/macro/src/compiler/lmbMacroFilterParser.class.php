<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbMacroFilterParser.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroFilterParser
{
  protected $text;
  protected $position;
  protected $length;
  protected $context;
  protected $filters = array();

  /**
  * Construct this parser
  */
  function __construct($context)
  {
    $this->context = $context;
  }

  protected function getToken($pattern)
  {
    if (preg_match($pattern, $this->text, $match, PREG_OFFSET_CAPTURE, $this->position))
    {
      $this->position += strlen($match[0][0]);
      return $match[1][0];
    }
    else
      return FALSE;
  }

  function getFilters()
  {
    return $this->filters;
  }

  /**
  * Parse text for expressions and emit a stream of events for expression fragments
  */
  function parse($text)
  {
    if (strlen($text) == 0)
      return array();

    $filters = array();

    $this->text = $text;
    $this->position = 0;

    $filters_expressions = $this->_extractFiltersExpressions();
    
    foreach($filters_expressions as $filter_expression)
    {
      $filter_spec = array();
      $result = preg_match('/\G\s*([A-Za-z][A-Za-z0-9_.]*)/u', $filter_expression, $match, PREG_OFFSET_CAPTURE);

      if(!$result)
         $this->context->raise('Filter name expected');

      $position = $match[0][1];
      $filter_name = $match[1][0];
      $filter_spec = array('name' => $filter_name,
                           'expression' => $filter_expression,
                           'params' => array());

      $params_expression = substr($filter_expression, strlen($filter_name));
      if(!$params_expression)
      {
        $filters[] = $filter_spec;
        continue;
      }

      if(strlen($params_expression))
      {
        $params_start_position = strpos($params_expression, ':');
        if(($params_start_position === FALSE))
          $this->context->raise('Unexpected symbol after filter name');
      }

      $params_str = substr($params_expression, $params_start_position + 1);
      if (strlen($params_str) == 0)
        $this->context->raise('Filter params expected after ":" symbol');

      $filter_spec['params'] = $this->_parseParams($params_str);
      $filters[] = $filter_spec; 
    }

    return $filters;
  }
  
  protected function _parseParams($params_string)
  {
    $params = array();
    
    $length = strlen($params_string);
    $this->position = 0;
    $this->text = $params_string;

    do
    {
      $token = $this->getToken('/\G("|\'|,|[^\'",]+)/u');
      if ($token === FALSE)
      {
        $filters_expressions[] = $this->text;
        break;
      }

      if ($token == '"' || $token == "'")
      {
        $string = $this->getToken('/\G([^' . $token . ']*' . $token . ')/u');

        if ($string === FALSE)
          $this->context->raise("Expecting a string literal in filter param");
      }
      elseif($token == ',')
      {
        $params[] = substr($this->text, 0, $this->position - 1);
        $this->text = substr($this->text, $this->position);
        $length = strlen($this->text);
        $this->position = 0;
      }
    }
    while($this->position < $length);

    //ensures the last param added
    $params[] = substr($this->text, 0, $this->position );
    
    return $params;
  }

  protected function _extractFiltersExpressions()
  {
    $length = strlen($this->text);
    $this->position = 0;

    $filters_expressions = array();
    do
    {
      $token = $this->getToken('/\G("|\'|\||[^\'"\|]+)/u');
      if ($token === FALSE)
      {
        $filters_expressions[] = $this->text;
        break;
      }

      if ($token == '"' || $token == "'")
      {
        $string = $this->getToken('/\G([^' . $token . ']*)' . $token . ',?/u');

        if ($string === FALSE)
          $this->context->raise("Expecting a string literal in filter param");
      }
      elseif($token == '|')
      {
        $filters_expressions[] = substr($this->text, 0, $this->position - 1);
        $this->text = substr($this->text, $this->position);
        $length = strlen($this->text);
        $this->position = 0;
      }
    }
    while($this->position < $length);

    //ensures the last filter expression added
    $filters_expressions[] = substr($this->text, 0, $this->position );

    return $filters_expressions;
  }
}

