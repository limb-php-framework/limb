<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactTagsToLowerSaxFilter.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/src/compiler/saxfilters/WactBaseSaxFilter.class.php';

class WactTagsToLowerSaxFilter extends WactBaseSaxFilter
{
  function startElement($tag, $attrs)
  {
    parent::startElement(strtolower($tag), array_change_key_case($attrs,CASE_LOWER));
  }


  function endElement($tag)
  {
    parent::endElement(strtolower($tag));
  }

  function emptyElement($tag, $attrs)
  {
    $this->ChildSaxFilter->emptyElement(strtolower($tag), $attrs);
  }
}
?>