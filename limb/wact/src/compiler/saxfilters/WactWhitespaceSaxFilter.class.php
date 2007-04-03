<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactWhitespaceSaxFilter.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/src/compiler/saxfilters/WactBaseSaxFilter.class.php';
/**
* SaxFilter for whitespace compression in template. Removes all whitespace
* except that inside a pre tag
*/
class WactWhitespaceSaxFilter extends WactBaseSaxFilter
{
  var $inHtml = FALSE;

  var $inPre = FALSE;

  function startElement($tag, $attrs)
  {
    switch ( strtolower($tag) )
    {
      case 'textarea':
      case 'script':
      case 'pre':
        $this->inPre = TRUE;
      break;
      case 'html':
        $this->inHtml = TRUE;
      break;
    }
    parent::startElement($tag, $attrs);
  }

  function endElement($tag)
  {
    switch ( strtolower($tag) )
    {
      case 'textarea':
      case 'script':
      case 'pre':
        $this->inPre = FALSE;
      break;
      case 'html':
        $this->inHtml = FALSE;
      break;
    }
    parent::endElement($Parser, $tag, $empty);
  }

  function characters($text)
  {
    if (!$this->inPre && $this->inHtml)
    {
      $text = trim($text);
      $text = preg_replace('/\s+/', ' ', $text);
    }
    parent :: characters($Parser, $text);
  }
}
?>