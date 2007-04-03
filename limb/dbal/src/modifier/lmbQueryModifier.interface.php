<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbQueryModifier.interface.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
interface lmbQueryModifier
{
  function applyTo($query);
}

?>