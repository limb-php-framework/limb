<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: literal.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
 * Prevents a section of the template from being parsed, placing the contents
 * directly into the compiled template
 * @tag core:LITERAL
 * @forbid_parsing
 */
class WactCoreLiteralTag extends WactCompilerTag
{
}
?>