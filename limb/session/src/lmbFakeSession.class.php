<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSession.class.php 5198 2007-03-06 17:53:46Z pachanga $
 * @package    session
 */
lmb_require('limb/session/src/lmbSession.class.php');

class lmbFakeSession extends lmbSession
{
  function start($storage = null){}
}
?>