<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDbDSNTest.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/lmbDbDSN.class.php');

class lmbDbDSNTest extends UnitTestCase
{
  function testConstructUsingString()
  {
    $dsn = new lmbDbDSN($str = 'mysql://wow:here@localhost/db');
    $this->assertEqual($dsn->getDriver(), 'mysql');
    $this->assertEqual($dsn->getUser(), 'wow');
    $this->assertEqual($dsn->getPassword(), 'here');
    $this->assertEqual($dsn->getHost(), 'localhost');
    $this->assertEqual($dsn->getDatabase(), 'db');
    $this->assertEqual($dsn->toString(), $str);
  }

  function testConstructUsingStringWithPort()
  {
    $dsn = new lmbDbDSN($str = 'mysql://wow:here@localhost:8080/db');
    $this->assertEqual($dsn->getDriver(), 'mysql');
    $this->assertEqual($dsn->getUser(), 'wow');
    $this->assertEqual($dsn->getPassword(), 'here');
    $this->assertEqual($dsn->getHost(), 'localhost');
    $this->assertEqual($dsn->getPort(), 8080);
    $this->assertEqual($dsn->getDatabase(), 'db');
    $this->assertEqual($dsn->toString(), $str);
  }

  function testConstructUsingStringWithExtraParameters()
  {
    $dsn = new lmbDbDSN($str = 'mysql://wow:here@localhost/db?param1=hey&param2=wow');
    $this->assertEqual($dsn->getDriver(), 'mysql');
    $this->assertEqual($dsn->getUser(), 'wow');
    $this->assertEqual($dsn->getPassword(), 'here');
    $this->assertEqual($dsn->getHost(), 'localhost');
    $this->assertEqual($dsn->getDatabase(), 'db');

    $this->assertEqual($dsn->getParam1(), 'hey');//extra parameters
    $this->assertEqual($dsn->getParam2(), 'wow');

    $this->assertEqual($dsn->toString(), $str);
  }

  function testConstructUsingArray()
  {
    $dsn = new lmbDbDSN(array('driver' => 'mysql',
                              'host' => 'localhost',
                              'user' => 'wow',
                              'password' => 'here',
                              'database' => 'db',
                              'port' => 8080));

    $this->assertEqual($dsn->getDriver(), 'mysql');
    $this->assertEqual($dsn->getUser(), 'wow');
    $this->assertEqual($dsn->getPassword(), 'here');
    $this->assertEqual($dsn->getHost(), 'localhost');
    $this->assertEqual($dsn->getPort(), 8080);
    $this->assertEqual($dsn->getDatabase(), 'db');
    $this->assertEqual($dsn->toString(), 'mysql://wow:here@localhost:8080/db');
  }

  function testConstructUsingArrayWithExtraParameters()
  {
    $dsn = new lmbDbDSN(array('driver' => 'mysql',
                              'host' => 'localhost',
                              'user' => 'wow',
                              'password' => 'here',
                              'database' => 'db',
                              'port' => 8080,
                              array('param1' => 'hey',
                                    'param2' => 'wow')));

    $this->assertEqual($dsn->getDriver(), 'mysql');
    $this->assertEqual($dsn->getUser(), 'wow');
    $this->assertEqual($dsn->getPassword(), 'here');
    $this->assertEqual($dsn->getHost(), 'localhost');
    $this->assertEqual($dsn->getPort(), 8080);
    $this->assertEqual($dsn->getDatabase(), 'db');

    $this->assertEqual($dsn->getParam1(), 'hey');//extra parameters
    $this->assertEqual($dsn->getParam2(), 'wow');

    $this->assertEqual($dsn->toString(), 'mysql://wow:here@localhost:8080/db?param1=hey&param2=wow');
  }
}

?>
