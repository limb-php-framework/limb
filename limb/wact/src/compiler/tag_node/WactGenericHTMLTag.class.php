<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Compile time component for tags in template which are not
 * recognized WACT tags but have a runat="server" attribute.
 * This allows native HTML tags, for example, to be manipulated
 * at runtime.
 * WactGenericHTMLTag is for tags with no children or content
 * @package wact
 * @version $Id: WactGenericHTMLTag.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class WactGenericHTMLTag extends WactRuntimeComponentHTMLTag
{
  protected $runtimeIncludeFile;
  protected $runtimeComponentName = 'WactRuntimeTagComponent';
}

