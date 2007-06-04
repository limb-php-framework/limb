<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactTemplateLocator.interface.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

interface WactTemplateLocator
{
  public function locateCompiledTemplate($fileName);
  public function locateSourceTemplate($fileName);

  public function readTemplateFile($fileName);
}
?>