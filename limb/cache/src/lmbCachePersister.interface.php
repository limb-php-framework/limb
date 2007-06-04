<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCachePersister.interface.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
define('LIMB_CACHE_NULL_RESULT', 'cache_null_' . md5(mt_rand()));

interface lmbCachePersister
{
  function getId();
  function put($key, $value, $group = 'default');
  function get($key, $group = 'default');
  function flushValue($key, $group = 'default');
  function flushGroup($group);
  function flushAll();
}
?>
