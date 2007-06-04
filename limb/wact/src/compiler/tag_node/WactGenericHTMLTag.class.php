<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactGenericHTMLTag.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

/**
* Compile time component for tags in template which are not
* recognized WACT tags but have a runat="server" attribute.
* This allows native HTML tags, for example, to be manipulated
* at runtime.
* WactGenericHTMLTag is for tags with no children or content
*/
class WactGenericHTMLTag extends WactRuntimeComponentHTMLTag
{
  protected $runtimeIncludeFile;
  protected $runtimeComponentName = 'WactRuntimeTagComponent';
}
?>