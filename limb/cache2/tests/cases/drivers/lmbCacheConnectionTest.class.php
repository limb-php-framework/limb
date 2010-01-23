<?php
/*
* Limb PHP Framework
*
* @link http://limb-project.com
* @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
* @license    LGPL http://www.gnu.org/copyleft/lesser.html
*/
lmb_require('limb/core/src/lmbObject.class.php');
lmb_require('limb/cache2/src/lmbCacheFactory.class.php');

class CacheableFooBarClass{}

abstract class lmbCacheConnectionTest extends UnitTestCase
{
  /**
   * @var lmbUri
   */
  protected $dsn;
  /**
   * @var lmbCacheAbstractConnection
   */
  protected $cache;

  protected $storage_init_file;

  function __construct()
  {
    if($this->storage_init_file)
      lmb_require($this->storage_init_file);
  }

  function setUp()
  {
    $this->cache = lmbCacheFactory::createConnection($this->dsn);
  }

  function tearDown()
  {
    @unlink(lmb_var_dir() . '/diff_thread.php');
    $this->cache->flush();
  }

  protected function _getUniqueId($prefix)
  {
    return $prefix . mt_rand();
  }

  function testGet_Negative()
  {
    $this->assertNull($this->cache->get($id = $this->_getUniqueId('testGet_Negative')));
  }

  function testGet_Positive()
  {
    $this->cache->set($id = $this->_getUniqueId('testGet_Positive'), $v = 'value');
    $var = $this->cache->get($id);
    $this->assertEqual($v, $var);
  }

  function testGet_Positive_Multiple()
  {
    $id1 = $this->_getUniqueId('testGet_Positive_Multiple1');
    $id2 = $this->_getUniqueId('testGet_Positive_Multiple2');
    $id3 = $this->_getUniqueId('testGet_Positive_Multiple3');

    $this->cache->set($id1, $v1 = 'value1');
    $this->cache->set($id2, $v2 = 'value2');

    $var = $this->cache->get(array($id1, $id2, $id3));

    $this->assertEqual($v1, $var[$id1]);
    $this->assertEqual($v2, $var[$id2]);
    $this->assertNull($var[$id3]);
  }

  function testGet_Positive_Multiple_WithZero()
  {
    $id1 = $this->_getUniqueId('testGet_Positive_Multiple_WithZero');

    $this->cache->add($id1, $v1 = 0);

    $var = $this->cache->get(array($id1));

    $this->assertEqual($v1, $var[$id1]);
  }

  function testGet_Positive_FalseValue()
  {
    $this->cache->set($id = $this->_getUniqueId('testGet_Positive_FalseValue'), $v = false);
    $var = $this->cache->get($id);
    $this->assertIdentical($var, $v);
  }

  function testAdd()
  {
    $this->cache->add($id = $this->_getUniqueId('testAdd'), $v = 'value');
    $var = $this->cache->get($id);
    $this->assertEqual($v, $var);
  }

  function testAddNonUnique()
  {
    $this->assertTrue($this->cache->add($id = $this->_getUniqueId('testAddNonUnique'), $v = 'value'));
    $this->assertFalse($this->cache->add($id, $v));
  }

  function testSet()
  {
    foreach($this->_getCachedValues() as $position => $v2)
    {
      $this->cache->set($id = $this->_getUniqueId('testSet'.$position), $v2);
      $cache_value = $this->cache->get($id);
      $this->assertIdentical($cache_value, $v2);
    }
  }

  function testDelete()
  {
    $this->cache->set($id1 = $this->_getUniqueId('testDelete1'), $v1 = 'value1');
    $this->cache->set($id2 = $this->_getUniqueId('testDelete2'), $v2 = 'value2');

    $this->cache->delete($id1);

    $this->assertFalse($this->cache->get($id1));

    $cache_value = $this->cache->get($id2);
    $this->assertEqual($cache_value, $v2);
  }

  function testFlush()
  {
    $this->cache->set($id = $this->_getUniqueId('testFlush1'), $value = 'value1');

    $this->cache->flush();

    $this->assertFalse($this->cache->get($id));
  }

  function testGetWithTtl_sameThread()
  {
    $value = 'value';
    $this->cache->set($id_short = $this->_getUniqueId('testGetWithTtl_sameThread1'), $value, $ttl = 1);
    $this->cache->set($id_long = $this->_getUniqueId('testGetWithTtl_sameThread2'), $value, $ttl = 10);
    sleep(2);
    $this->assertNull($this->cache->get($id_short));
    $this->assertIdentical($value, $this->cache->get($id_long));
  }

  function testGetWithTtl_differentThread()
  {
    $value = 'value';
    $this->cache->set($id_short = $this->_getUniqueId('testGetWithTtl_differentThread1'), $value, $ttl = 1);
    $this->cache->set($id_long = $this->_getUniqueId('testGetWithTtl_differentThread2'), $value, $ttl = 10);
    sleep(2);
    $this->assertNull($this->_makeGetFromDifferentThread($id_short));
    $this->assertIdentical($value, $this->_makeGetFromDifferentThread($id_long));

  }

  function testProperSerializing()
  {
    $obj = new lmbObject();
    $obj->set('foo', 'wow');

    $this->cache->set($id = $this->_getUniqueId('testProperSerializing'), $obj);

    $this->assertEqual($obj, $this->cache->get($id));
  }

  function testObjectClone()
  {
    $value = 'bar';

    $obj = new lmbObject();
    $obj->set('foo', $value);

    $this->cache->set($id = $this->_getUniqueId('testObjectClone'), $obj);

    $obj->set('foo', 'new value');

    $cached_obj = $this->cache->get($id);
    if($this->assertIsA($cached_obj, 'lmbObject'))
      $this->assertEqual($value, $cached_obj->get('foo'));
  }

  function testWithPrefix_NotIntercepting()
  {
    $dsn = $this->dsn;
    if(!is_object($dsn))
    $dsn = new lmbUri($dsn);

    $cache = lmbCacheFactory::createConnection($dsn);

    $dsn_with_prefix = clone($dsn);
    $dsn_with_prefix->addQueryItem('prefix', 'foo');
    $cache_with_prefix = lmbCacheFactory::createConnection($dsn_with_prefix);

    $id = $this->_getUniqueId('testWithPrefix_NotIntercepting');
    $cache->set($id, 42);
    $cache_with_prefix->set($id, 24);

    $this->assertEqual(42, $cache->get($id));
  }

  function testIncrementAndDecrement()
  {
    $key = $this->_getUniqueId('testIncrementAndDecrement');

    $this->assertFalse($this->cache->increment($key));

    $this->cache->set($key.'1', "string");
    $this->assertEqual(1, $this->cache->increment($key.'1'));

    $this->cache->set($key.'2', 0);
    $this->assertEqual(1, $this->cache->increment($key.'2'));

    $this->cache->set($key.'3', 1);
    $this->cache->increment($key.'3', 10);
    $this->assertEqual(11, $this->cache->get($key.'3'));

    $this->cache->set($key.'4', 11);
    $this->cache->decrement($key.'4', 1);
    $this->assertEqual(10, $this->cache->get($key.'4'));

    $this->cache->set($key.'5', 11);
    $this->cache->decrement($key.'5', 100);
    $this->assertEqual(0, $this->cache->get($key.'5'));
  }

  function testSafeIncrement()
  {
    $key = $this->_getUniqueId('testSafeIncrement');
    $this->assertEqual(1, $this->cache->safeIncrement($key));
  }

  function testSafeDecrement()
  {
    $key = $this->_getUniqueId('testSafeDecrement');
    $this->assertEqual(0, $this->cache->safeDecrement($key));
    $this->assertFalse(null === $this->cache->get($key));
  }

  function testLock()
  {
    $this->assertTrue($this->cache->lock($id = $this->_getUniqueId('testLock')));
    $this->assertFalse($this->cache->lock($id));
  }

  function testUnlock()
  {
    $this->assertTrue($this->cache->lock($id = $this->_getUniqueId('testUnlock')));
    $this->cache->unlock($id);
    $this->assertTrue($this->cache->lock($id));
  }

  protected function _makeGetFromDifferentThread($id)
  {
      return $this->_makeCallFromDifferentThread('get', array($id));
  }

  protected function _makeCallFromDifferentThread($method, $arguments)
  {
    $filename = lmb_var_dir() . '/diff_thread.php';
    $cur_file_dir = dirname(__FILE__);
    $include_path = get_include_path();
    $cur_process_dir = getcwd();
    $arguments_str = implode("', '", $arguments);
    $setup_file = realpath($cur_file_dir.'/../../../common.inc.php');

    $request_code = <<<EOD
<?php
    ob_start();
    chdir('$cur_process_dir');
    set_include_path('$include_path');
    require_once('$setup_file');

    lmb_require('limb/cache2/src/lmbCacheFactory.class.php');
    \$cache = lmbCacheFactory::createConnection('{$this->dsn}');
    ob_end_clean();
    echo serialize(\$cache->$method('$arguments_str'));
EOD;
    $storage_init_file = $limb_db_dsn = $limb_var_dir = '';

    if($this->storage_init_file)
      $storage_init_file = "lmb_require('{$this->storage_init_file}');";

    if(lmb_env_has('LIMB_DB_DSN'))
      $limb_db_dsn = "lmb_env_setor('LIMB_DB_DSN', '" . lmb_env_get('LIMB_DB_DSN') . "');";

    if(lmb_env_has('LIMB_VAR_DIR'))
      $limb_var_dir = "lmb_env_setor('LIMB_VAR_DIR', '" . lmb_env_get('LIMB_VAR_DIR') . "');";

    $request_code = sprintf($request_code, $storage_init_file, $limb_db_dsn, $limb_var_dir);

    file_put_contents($filename, $request_code);
    $result = shell_exec("php $filename");
    return unserialize($result);
  }

  function _getCachedValues()
  {
    return array(
      NULL,
      'some value',
      array('some value'),
      new CacheableFooBarClass(),
   );
  }
}
