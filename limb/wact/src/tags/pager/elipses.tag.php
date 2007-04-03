<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: elipses.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* Compile time component for elispses in a pager.
* Elipses are sed to mark omitted page numbers outside of the
* current range of the pager e.g. ...6 7 8... (the ... are the elipses)
* @tag pager:ELIPSES
* @restrict_self_nesting
* @parent_tag_class WactPagerListTag
*/
class WactPagerElipsesTag extends WactSilentCompilerTag
{
}

?>