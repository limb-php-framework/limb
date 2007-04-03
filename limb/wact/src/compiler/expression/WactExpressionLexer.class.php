<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactExpressionLexer.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**#@+
 * lexer mode constant
 */
define("EXPRESSION_LEXER_ENTER", 1);
define("EXPRESSION_LEXER_MATCHED", 2);
define("EXPRESSION_LEXER_UNMATCHED", 3);
define("EXPRESSION_LEXER_EXIT", 4);
define("EXPRESSION_LEXER_SPECIAL", 5);
/**#@-*/

/**
 *    Accepts text and breaks it into tokens.
 *    Some optimisation to make the sure the
 *    content is only scanned by the PHP regex
 *    parser once. Lexer modes must not start
 *    with leading underscores.
 *    @package wact
 */
class WactExpressionLexer {
  var $_regexes;
  var $_parser;
  var $_mode;
  var $_mode_handlers;
  var $_case;

  /**
   *    Sets up the lexer in case insensitive matching
   *    by default.
   *    @param ExpressionParser $parser  Handling strategy by
   *                                    reference.
   *    @param string $start            Starting handler.
   *    @param boolean $case            True for case sensitive.
   *    @access public
   */
  function WactExpressionLexer($parser, $start = "accept", $case = false) {
    $this->_case = $case;
    $this->_regexes = array();
    $this->_parser = $parser;
    $this->_mode = new WactExpressionLexerStateStack($start);
    $this->_mode_handlers = array();
  }

  /**
   *    Adds a token search pattern for a particular
   *    parsing mode. The pattern does not change the
   *    current mode.
   *    @param string $pattern      Perl style regex, but ( and )
   *                                lose the usual meaning.
   *    @param string $mode         Should only apply this
   *                                pattern when dealing with
   *                                this type of input.
   *    @access public
   */
  function addPattern($pattern, $mode = "accept") {
    if (! isset($this->_regexes[$mode])) {
      $this->_regexes[$mode] = new WactExpressionLexerParallelRegex($this->_case);
    }
    $this->_regexes[$mode]->addPattern($pattern);
  }

  /**
   *    Adds a pattern that will enter a new parsing
   *    mode. Useful for entering parenthesis, strings,
   *    tags, etc.
   *    @param string $pattern      Perl style regex, but ( and )
   *                                lose the usual meaning.
   *    @param string $mode         Should only apply this
   *                                pattern when dealing with
   *                                this type of input.
   *    @param string $new_mode     Change parsing to this new
   *                                nested mode.
   *    @access public
   */
  function addEntryPattern($pattern, $mode, $new_mode) {
    if (! isset($this->_regexes[$mode])) {
      $this->_regexes[$mode] = new WactExpressionLexerParallelRegex($this->_case);
    }
    $this->_regexes[$mode]->addPattern($pattern, $new_mode);
  }

  /**
   *    Adds a pattern that will exit the current mode
   *    and re-enter the previous one.
   *    @param string $pattern      Perl style regex, but ( and )
   *                                lose the usual meaning.
   *    @param string $mode         Mode to leave.
   *    @access public
   */
  function addExitPattern($pattern, $mode) {
    if (! isset($this->_regexes[$mode])) {
      $this->_regexes[$mode] = new WactExpressionLexerParallelRegex($this->_case);
    }
    $this->_regexes[$mode]->addPattern($pattern, "__exit");
  }

  /**
   *    Adds a pattern that has a special mode. Acts as an entry
   *    and exit pattern in one go, effectively calling a special
   *    parser handler for this token only.
   *    @param string $pattern      Perl style regex, but ( and )
   *                                lose the usual meaning.
   *    @param string $mode         Should only apply this
   *                                pattern when dealing with
   *                                this type of input.
   *    @param string $special      Use this mode for this one token.
   *    @access public
   */
  function addSpecialPattern($pattern, $mode, $special) {
    if (! isset($this->_regexes[$mode])) {
      $this->_regexes[$mode] = new WactExpressionLexerParallelRegex($this->_case);
    }
    $this->_regexes[$mode]->addPattern($pattern, "_$special");
  }

  /**
   *    Adds a mapping from a mode to another handler.
   *    @param string $mode        Mode to be remapped.
   *    @param string $handler     New target handler.
   *    @access public
   */
  function mapHandler($mode, $handler) {
    $this->_mode_handlers[$mode] = $handler;
  }

  /**
   *    Splits the page text into tokens. Will fail
   *    if the handlers report an error or if no
   *    content is consumed. If successful then each
   *    unparsed and parsed token invokes a call to the
   *    held listener.
   *    @param string $raw        Raw HTML text.
   *    @return boolean           True on success, else false.
   *    @access public
   */
  function parse($raw) {
    if (! isset($this->_parser)) {
      return false;
    }
    $length = strlen($raw);
    while (is_array($parsed = $this->_reduce($raw))) {
      list($unmatched, $matched, $mode) = $parsed;
      if (! $this->_dispatchTokens($unmatched, $matched, $mode)) {
        return false;
      }
      if (strlen($raw) == $length) {
        return false;
      }
      $length = strlen($raw);
    }
    if (!$parsed) {
      return false;
    }
    return $this->_invokeParser($raw, EXPRESSION_LEXER_UNMATCHED);
  }

  /**
   *    Sends the matched token and any leading unmatched
   *    text to the parser changing the lexer to a new
   *    mode if one is listed.
   *    @param string $unmatched    Unmatched leading portion.
   *    @param string $matched      Actual token match.
   *    @param string $mode         Mode after match. A boolean
   *                                false mode causes no change.
   *    @return boolean             False if there was any error
   *                                from the parser.
   *    @access private
   */
  function _dispatchTokens($unmatched, $matched, $mode = false) {
    if (! $this->_invokeParser($unmatched, EXPRESSION_LEXER_UNMATCHED)) {
      return false;
    }
    if ($this->_isModeEnd($mode)) {
      if (! $this->_invokeParser($matched, EXPRESSION_LEXER_EXIT)) {
        return false;
      }
      return $this->_mode->leave();
    }
    if ($this->_isSpecialMode($mode)) {
      $this->_mode->enter($this->_decodeSpecial($mode));
      if (! $this->_invokeParser($matched, EXPRESSION_LEXER_SPECIAL)) {
        return false;
      }
      return $this->_mode->leave();
    }
    if (is_string($mode)) {
      $this->_mode->enter($mode);
      return $this->_invokeParser($matched, EXPRESSION_LEXER_ENTER);
    }
    return $this->_invokeParser($matched, EXPRESSION_LEXER_MATCHED);
  }

  /**
   *    Tests to see if the new mode is actually to leave
   *    the current mode and pop an item from the matching
   *    mode stack.
   *    @param string $mode    Mode to test.
   *    @return boolean        True if this is the exit mode.
   *    @access private
   */
  function _isModeEnd($mode) {
    return ($mode === "__exit");
  }

  /**
   *    Test to see if the mode is one where this mode
   *    is entered for this token only and automatically
   *    leaves immediately afterwoods.
   *    @param string $mode    Mode to test.
   *    @return boolean        True if this is the exit mode.
   *    @access private
   */
  function _isSpecialMode($mode) {
    return (strncmp($mode, "_", 1) == 0);
  }

  /**
   *    Strips the magic underscore marking single token
   *    modes.
   *    @param string $mode    Mode to decode.
   *    @return string         Underlying mode name.
   *    @access private
   */
  function _decodeSpecial($mode) {
    return substr($mode, 1);
  }

  /**
   *    Calls the parser method named after the current
   *    mode. Empty content will be ignored. The lexer
   *    has a parser handler for each mode in the lexer.
   *    @param string $content        Text parsed.
   *    @param boolean $is_match      Token is recognised rather
   *                                  than unparsed data.
   *    @access private
   */
  function _invokeParser($content, $is_match) {
    if (($content === "") || ($content === false)) {
      return true;
    }
    $handler = $this->_mode->getCurrent();
    if (isset($this->_mode_handlers[$handler])) {
      $handler = $this->_mode_handlers[$handler];
    }
    return $this->_parser->$handler($content, $is_match);
  }

  /**
   *    Tries to match a chunk of text and if successful
   *    removes the recognised chunk and any leading
   *    unparsed data. Empty strings will not be matched.
   *    @param string $raw         The subject to parse. This is the
   *                               content that will be eaten.
   *    @return array              Three item list of unparsed
   *                               content followed by the
   *                               recognised token and finally the
   *                               action the parser is to take.
   *                               True if no match, false if there
   *                               is a parsing error.
   *    @access private
   */
  function _reduce(&$raw) {
    if (! isset($this->_regexes[$this->_mode->getCurrent()])) {
      return false;
    }
    if ($raw === "") {
      return true;
    }
    if ($action = $this->_regexes[$this->_mode->getCurrent()]->match($raw, $match)) {
      $unparsed_character_count = strpos($raw, $match);
      $unparsed = substr($raw, 0, $unparsed_character_count);
      $raw = substr($raw, $unparsed_character_count + strlen($match));
      return array($unparsed, $match, $action);
    }
    return true;
  }
}
?>