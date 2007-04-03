<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbIpTest.class.php 5001 2007-02-08 15:36:45Z pachanga $
 * @package    net
 */
lmb_require('limb/net/src/lmbIp.class.php');

class lmbIpTest extends UnitTestCase
{
  var $ip = null;

  function setUp()
  {
    $this->ip = new lmbIp();
  }

  function testIsValid()
  {
    $this->assertTrue(lmbIp :: isValid('127.0.0.1'));
    $this->assertTrue(lmbIp :: isValid('255.255.255.255'));

    $this->assertFalse(lmbIp :: isValid('wow'));
    $this->assertFalse(lmbIp :: isValid('256.255.255.255'));
  }

  function testEncodeIpRangeFailure()
  {
    try
    {
      $this->ip->encodeIpRange('bla', 'foo');
      $this->assertTrue(false);
    }
    catch(lmbException $e){}

    try
    {
      $this->ip->encodeIpRange('127.0.0.1', 'foo');
      $this->assertTrue(false);
    }
    catch(lmbException $e){}

    try
    {
      $this->ip->encodeIpRange('bla', '127.0.0.1');
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testEncodeIpRange()
  {
    $ip_list = $this->ip->encodeIpRange('192.168.0.1', '192.168.10.10');

    $this->assertNotIdentical(false, array_search($this->ip->encode('192.168.0.1'), $ip_list));
    $this->assertNotIdentical(false, array_search($this->ip->encode('192.168.10.10'), $ip_list));
  }

  function testEncodeDecode()
  {
    $this->assertEqual(lmbIp :: encode('127.0.0.1'), ip2long('127.0.0.1'));
    $this->assertEqual(lmbIp :: decode(ip2long('127.0.0.1')), '127.0.0.1');
  }
}

?>