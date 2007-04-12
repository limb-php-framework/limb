<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactExpressionLexerParallelRegex.class.php 5646 2007-04-12 08:38:15Z pachanga $
 * @package    wact
 */

/**
 *    Compounded regular expression. Any of
 *    the contained patterns could match and
 *    when one does it's label is returned.
 *    @package    wact
 */
class WactExpressionLexerParallelRegex {
  protected $_patterns;
  protected $_labels;
  protected $_regex;
  protected $_case;

  /**
   *    Constructor. Starts with no patterns.
   *    @param boolean $case    True for case sensitive, false
   *                            for insensitive.
   *    @access public
   */
  function WactExpressionLexerParallelRegex($case) {
    $this->_case = $case;
    $this->_patterns = array();
    $this->_labels = array();
    $this->_regex = null;
  }

  /**
   *    Adds a pattern with an optional label.
   *    @param string $pattern      Perl style regex, but ( and )
   *                                lose the usual meaning.
   *    @param string $label        Label of regex to be returned
   *                                on a match.
   *    @access public
   */
  function addPattern($pattern, $label = true) {
    $count = count($this->_patterns);
    $this->_patterns[$count] = $pattern;
    $this->_labels[$count] = $label;
    $this->_regex = null;
  }

  /**
   *    Attempts to match all patterns at once against
   *    a string.
   *    @param string $subject      String to match against.
   *    @param string $match        First matched portion of
   *                                subject.
   *    @return boolean             True on success.
   *    @access public
   */
  function match($subject, &$match) {
    if (count($this->_patterns) == 0) {
      return false;
    }
    if (! preg_match($this->_getCompoundedRegex(), $subject, $matches)) {
      $match = "";
      return false;
    }
    $match = $matches[0];
    for ($i = 1; $i < count($matches); $i++) {
      if ($matches[$i] || ($matches[$i] !== '')) {
        return $this->_labels[$i - 1];
      }
    }
    return true;
  }

  /**
   *    Compounds the patterns into a single
   *    regular expression separated with the
   *    "or" operator. Caches the regex.
   *    Will automatically escape (, ) and / tokens.
   *    @param array $patterns    List of patterns in order.
   *    @access private
   */
  function _getCompoundedRegex() {
    if ($this->_regex == null) {
      for ($i = 0; $i < count($this->_patterns); $i++) {
        $this->_patterns[$i] = '(' . str_replace(
            array('/', '(', ')'),
            array('\/', '\(', '\)'),
            $this->_patterns[$i]) . ')';
      }
      $this->_regex = "/" . implode("|", $this->_patterns) . "/" . $this->_getPerlMatchingFlags();
    }
    return $this->_regex;
  }

  /**
   *    Accessor for perl regex mode flags to use.
   *    @return string       Perl regex flags.
   *    @access private
   */
  function _getPerlMatchingFlags() {
    return ($this->_case ? "msS" : "msSi");
  }
}
?>