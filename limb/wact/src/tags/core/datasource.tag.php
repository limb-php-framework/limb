<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: datasource.tag.php 5878 2007-05-13 11:14:57Z serega $
 * @package    wact
 */

/**
* Datasources act is "namespaces" for a template.
* @tag core:DATASOURCE
* @convert_to_expression from
*/
class WactCoreDatasourceTag extends WactRuntimeDatasourceComponentTag
{
  protected $runtimeComponentName = 'WactDatasourceRuntimeComponent';
}
?>