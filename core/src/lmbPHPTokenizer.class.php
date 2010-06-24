<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * The scanner tokenizes input content ({@link input()})
 * and provides a "scanner" interface which can go forward
 * ({@link next()}) and backward (({@link back()}) )
 * along the tokens. It also correlates the toekens with line
 * number ({@link line()}).
 *
 * Part of the credits for this scanner is due to phpDocumentor,
 * from which I borrowed some ideas to make it work.
 *
 * @author Oak Nauhygon <ezpdo4php@gmail.com>
 * @package core
 * @version $Id$
 */
class lmbPHPTokenizer
{
  /**
   * List of tokens that can contain a newline
   * @var array
   */
  static public $newline_tokens = array(
      T_WHITESPACE,
      T_ENCAPSED_AND_WHITESPACE,
      T_COMMENT,
      T_DOC_COMMENT,
      T_OPEN_TAG,
      T_CLOSE_TAG,
      T_INLINE_HTML
      );

  /**
   * The input content
   * @var string
   */
  protected $input;

  /**
   * tokenized array from {@link token_get_all()}
   * @var array
   */
  protected $tokens;

  /**
   * current token position
   * @var integer
   */
  protected $pos = 0;

  /**
   * current source line number
   * @var integer
   */
  protected $line = 0;

  /**
   * Constructor
   * @param string input content
   */
  function __construct($input = '') 
  {
    if(!empty($input)) 
      $this->input($input);
  }

  /**
   * get input if no param supplied or set input if param is a non-empty string
   * @param string input content
   * @return string|bool
   */
  function input($input = false) 
  {
    // if input is false, return
    if($input === false) 
      return $this->input;

    // trim the \r\n input content
    $input = rtrim(ltrim($input, "\r\n"));
    if(empty($input)) 
      return false;

    // use reference to save memory
    $this->input = & $input;

    // unset the tokens so when next() is called the frist
    // time, it will call reset()
    unset($this->tokens);

    return true;
  }

  /**
   * Tokenize input content and reset current
   * token position and line number
   * @return void
   */
  function reset() 
  {
    $this->tokens = @token_get_all($this->input);
    $this->pos = 0;
    $this->line = 0;
  }

  /**
   * Fetch the next token
   * @return string|array token from tokenizer
   */
  function next() 
  {
    // check if we need to reset (tokenize input)
    if(empty($this->tokens))
      $this->reset();

    // check if token at the cur position set
    if(!isset($this->tokens[$this->pos])) 
      return false;

    // keep track of the old line
    $oldline = $this->line;

    // now get the current token
    $word = $this->tokens[$this->pos++];

    // correlate line and token
    if(is_array($word)) 
    {
      // count line num for special tokens ({@link $newline_tokens})
      if(in_array($word[0], lmbPHPTokenizer::$newline_tokens)) 
        $this->line += substr_count($word[1], "\n");

      // always skip whitespace
      if($word[0] == T_WHITESPACE)    
        return $this->next();
    }

    return $word;
  }

  /**
   * Go back one token (reverse of {@link next()})
   * @return false|string|array
   */
  function back() 
  {
    $this->pos--;

    // check if it's the beginning
    if($this->pos < 0) 
    {
      $this->pos = 0;
      return false;
    }

    $word = $this->tokens[$this->pos];

    if(is_array($word)) 
    {
      if($word[0] == T_WHITESPACE)
        return $this->next();

      if(in_array($word[0], lmbPHPTokenizer::$newline_tokens)) 
        $this->line -= substr_count($word[1], "\n");
    }
  }

  /**
   * Get the current line number
   * @return integer
   */
  function line() 
  {
    return $this->line;
  }
}


