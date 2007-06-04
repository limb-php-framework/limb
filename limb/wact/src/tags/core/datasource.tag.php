<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: datasource.tag.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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