<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    $package$
 */

/**
* Purpose of the classes here are to provide a dictionary of
* the "special cases" that exist in HTML only so that the template
* parser will know what to do with them, when used as a component
*/

/**
 * @tag br
 * @forbid_end_tag
 * @runat client
 * @restrict_self_nesting
 */
class BrGenericTag extends WactGenericHTMLTag{}

/**
 * @tag hr
 * @forbid_end_tag
 * @runat client
 * @restrict_self_nesting
 */
class HrGenericTag extends WactGenericHTMLTag{}

/**
 * @tag img
 * @forbid_end_tag
 * @runat client
 * @restrict_self_nesting
 */
class ImgGenericTag extends WactGenericHTMLTag{}

/**
 * @tag link
 * @forbid_end_tag
 * @runat client
 * @restrict_self_nesting
 */
class LinkGenericTag extends WactGenericHTMLTag{}

/**
 * @tag p
 * @runat client
 * @restrict_self_nesting
 */
class PGenericTag extends WactGenericContainerHTMLTag{}

/**
 * @tag param
 * @forbid_end_tag
 * @runat client
 * @restrict_self_nesting
 */
class ParamGenericTag extends WactGenericHTMLTag{}

/**
 * @tag meta
 * @forbid_end_tag
 * @runat client
 * @restrict_self_nesting
 */
class MetaGenericTag extends WactGenericHTMLTag{}

?>