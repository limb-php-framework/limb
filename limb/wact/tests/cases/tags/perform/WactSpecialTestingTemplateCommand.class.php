<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    wact
 */
require_once('limb/wact/src/components/perform/WactTemplateCommand.class.php');

class WactSpecialTestingTemplateCommand extends WactTemplateCommand
{
  function doPerform($value1, $value2)
  {
    $this->template->set('my_var', $value1); // root component is always a datasource
    $this->context_component->getDatasourceComponent()->set('my_var', $value2);
  }
}
?>
