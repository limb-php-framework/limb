<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/net/src/lmbIp.class.php');

class lmbIpTest extends UnitTestCase
{
  function testIsValid()
  {
    $this->assertTrue(lmbIp::isValid('127.0.0.1'));
    $this->assertTrue(lmbIp::isValid('255.255.255.255'));

    $this->assertFalse(lmbIp::isValid('wow'));
    $this->assertFalse(lmbIp::isValid('256.255.255.255'));
  }

  function testEncodeDecodeSigned()
  {
    //no overflow
    $this->assertIdentical(lmbIp::encode('127.0.0.0', lmbIp::SIGNED), 2130706432);
    $this->assertIdentical(lmbIp::decode(2130706432), '127.0.0.0');

    //with overflow
    $this->assertIdentical(lmbIp::encode('128.0.0.0', lmbIp::SIGNED), (int)-2147483648);
    $this->assertIdentical(lmbIp::decode(-2147483648), '128.0.0.0');
  }

  function testEncodeDecodeUnsigned()
  {
    //no overflow
    $this->assertIdentical(lmbIp::encode('127.0.0.0', lmbIp::UNSIGNED), 2130706432);
    $this->assertIdentical(lmbIp::decode(2130706432), '127.0.0.0');

    //with overflow
    $this->assertIdentical(lmbIp::encode('128.0.0.0', lmbIp::UNSIGNED), 2147483648.0);
    $this->assertIdentical(lmbIp::decode(2147483648), '128.0.0.0');
  }

  function testEncodeDecodeUnsignedString()
  {
    //no overflow
    $this->assertIdentical(lmbIp::encode('127.0.0.0', lmbIp::USTRING), '2130706432');
    $this->assertIdentical(lmbIp::decode('2130706432'), '127.0.0.0');

    //with overflow
    $this->assertIdentical(lmbIp::encode('128.0.0.0', lmbIp::USTRING), '2147483648');
    $this->assertIdentical(lmbIp::decode('2147483648'), '128.0.0.0');
  }

  function testEncodeIpRangeFailure()
  {
    try
    {
      lmbIp::encodeIpRange('bla', 'foo');
      $this->assertTrue(false);
    }
    catch(lmbException $e){}

    try
    {
      lmbIp::encodeIpRange('127.0.0.1', 'foo');
      $this->assertTrue(false);
    }
    catch(lmbException $e){}

    try
    {
      lmbIp::encodeIpRange('bla', '127.0.0.1');
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testEncodeIpRange()
  {
    $ip_list = lmbIp::encodeIpRange('192.168.0.1', '192.168.10.10', lmbIp::UNSIGNED);

    $this->assertNotIdentical(false, array_search(lmbIp::encode('192.168.0.1', lmbIp::UNSIGNED), $ip_list));
    $this->assertNotIdentical(false, array_search(lmbIp::encode('192.168.10.10', lmbIp::UNSIGNED), $ip_list));
  }

}


