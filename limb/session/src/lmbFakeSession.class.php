<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/session/src/lmbSession.class.php');

/**
 * class lmbFakeSession.
 *
 * @package session
 * @version $Id$
 */
class lmbFakeSession extends lmbSession
{
  function start($storage = null){}
}

