<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCachePersister.interface.php 4985 2007-02-08 15:35:06Z pachanga $
 * @package    cache
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
